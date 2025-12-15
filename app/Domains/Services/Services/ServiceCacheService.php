<?php

declare(strict_types=1);

namespace App\Domains\Services\Services;

use App\Domains\Services\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Redis\Connections\Connection;

class ServiceCacheService
{
    private const CACHE_VERSION = 'v2';
    private const STALE_TTL = 86400; // 24 hours for stale-while-revalidate
    private $supportsTags;

    public function __construct()
    {
        // Проверяем, поддерживает ли текущий драйвер кэша тегирование
        $cacheDriver = config('cache.default');
        $this->supportsTags = in_array($cacheDriver, ['redis', 'memcached', 'array']);
    }

    /**
     * Получить Redis соединение
     */
    private function redis(): Connection
    {
        return Redis::connection();
    }

    /**
     * Проверить, используется ли Redis как драйвер кэша
     */
    private function isRedisCache(): bool
    {
        return config('cache.default') === 'redis';
    }

    /**
     * Генерирует ключ с поддержкой тегов
     */
    private function generateKeyWithTags(string $baseKey, array $tags): string
    {
        if (!$this->supportsTags) {
            // Если теги не поддерживаются, добавляем теги в ключ
            sort($tags);
            $tagSuffix = implode('_', array_map('str_slug', $tags));
            return $baseKey . '_tags_' . md5($tagSuffix);
        }
        return $baseKey;
    }

    public function generateIndexKey(Request $request): string
    {
        $locale = app()->getLocale();
        $fingerprint = md5(serialize($request->query()));

        return sprintf('services:index:%s:%s:%s',
            self::CACHE_VERSION,
            $locale,
            $fingerprint
        );
    }

    public function generateShowKey(string $slug): string
    {
        $locale = app()->getLocale();

        return sprintf('services:show:%s:%s:%s',
            self::CACHE_VERSION,
            $locale,
            $slug
        );
    }

    public function generateFilterKey(Request $request): string
    {
        $locale = app()->getLocale();
        $fingerprint = md5(serialize($request->all()));

        return sprintf('services:filter:%s:%s:%s',
            self::CACHE_VERSION,
            $locale,
            $fingerprint
        );
    }

    /**
     * Cache with fallback strategy for high availability
     */
    public function rememberWithFallback(
        string $key,
        array $tags,
        int $ttl,
        callable $callback,
        callable $fallback
    ) {
        try {
            // Генерируем ключ с учетом тегов
            $cacheKey = $this->generateKeyWithTags($key, $tags);

            // Пытаемся получить из кэша
            if ($this->supportsTags) {
                $cached = Cache::tags($tags)->get($key);
            } else {
                $cached = Cache::get($cacheKey);
            }

            if ($cached !== null) {
                return $cached;
            }

            // Execute callback and cache result
            $result = $callback();

            // Store in cache with TTL
            if ($this->supportsTags) {
                Cache::tags($tags)->put($key, $result, $ttl);
            } else {
                Cache::put($cacheKey, $result, $ttl);
            }

            // Also store in Redis for faster access
            if ($this->isRedisCache()) {
                $this->redis()->command('SETEX', [$key, $ttl, serialize($result)]);
            }

            return $result;

        } catch (Exception $e) {
            // Log error and use fallback
            Log::warning('Cache failure for key: ' . $key, [
                'error' => $e->getMessage(),
                'tags' => $tags
            ]);

            return $fallback();
        }
    }

    /**
     * Implement stale-while-revalidate pattern
     */
    public function rememberWithStaleWhileRevalidate(
        string $key,
        array $tags,
        int $ttl,
        callable $callback
    ) {
        $cacheKey = $this->generateKeyWithTags($key, $tags);

        if ($this->supportsTags) {
            $value = Cache::tags($tags)->get($key);
            $hasKey = Cache::tags($tags)->has($key);
        } else {
            $value = Cache::get($cacheKey);
            $hasKey = Cache::has($cacheKey);
        }

        // Check if cache is stale but still valid
        if ($value && $hasKey) {
            // Refresh cache asynchronously if stale
            if ($this->isStale($key)) {
                dispatch(function () use ($key, $tags, $ttl, $callback) {
                    try {
                        $freshValue = $callback();

                        if ($this->supportsTags) {
                            Cache::tags($tags)->put($key, $freshValue, $ttl);
                        } else {
                            $cacheKey = $this->generateKeyWithTags($key, $tags);
                            Cache::put($cacheKey, $freshValue, $ttl);
                        }
                    } catch (Exception $e) {
                        Log::error('Background cache refresh failed', [
                            'key' => $key,
                            'error' => $e->getMessage()
                        ]);
                    }
                })->afterResponse();
            }

            return $value;
        }

        // Cache miss - compute and store
        $freshValue = $callback();

        if ($freshValue !== null) {
            if ($this->supportsTags) {
                Cache::tags($tags)->put($key, $freshValue, $ttl);
            } else {
                Cache::put($cacheKey, $freshValue, $ttl);
            }
        }

        return $freshValue;
    }

    private function isStale(string $key): bool
    {
        if (!$this->isRedisCache()) {
            return false;
        }

        try {
            $ttl = $this->redis()->command('TTL', [$key]);

            // Consider stale if TTL is less than 10% of original
            return $ttl > 0 && $ttl < 360; // 6 minutes for 1 hour TTL
        } catch (Exception $e) {
            Log::warning('Failed to check Redis TTL', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function incrementViewCount(string $slug): void
    {
        if (!$this->isRedisCache()) {
            return;
        }

        try {
            $redis = $this->redis();
            $key = sprintf('service:views:%s:%s', date('Y-m-d'), $slug);

            // Increment daily counter
            $redis->command('INCR', [$key]);
            $redis->command('EXPIRE', [$key, 86400]); // Keep for 24 hours

            // Update weekly/monthly sorted sets
            $redis->command('ZINCRBY', ['services:popularity:weekly', 1, $slug]);
            $redis->command('ZINCRBY', ['services:popularity:monthly', 1, $slug]);

        } catch (Exception $e) {
            Log::error('Failed to increment view count', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Invalidate cache intelligently (for admin panel webhooks)
     */
    public function invalidateService(string $slug): void
    {
        // Очищаем кэш конкретной услуги
        if ($this->supportsTags) {
            Cache::tags(['services.show', 'service.' . $slug])->flush();
        } else {
            // Без тегов - удаляем по паттерну ключей
            $pattern = sprintf('*services*%s*', $slug);
            $this->deleteKeysByPattern($pattern);
        }

        // Очищаем связанные кэши
        $tagsToClear = ['services.index', 'services.filter', 'services.category'];
        foreach ($tagsToClear as $tag) {
            if ($this->supportsTags) {
                Cache::tags([$tag])->flush();
            } else {
                $pattern = sprintf('*%s*', str_replace(':', '_', $tag));
                $this->deleteKeysByPattern($pattern);
            }
        }

        // Clear Redis cache if used
        if ($this->isRedisCache()) {
            try {
                $redis = $this->redis();
                $pattern = sprintf('services:*%s*', $slug);

                // Use SCAN instead of KEYS for production safety
                $cursor = 0;
                do {
                    $result = $redis->command('SCAN', [$cursor, 'MATCH', $pattern, 'COUNT', 100]);
                    $cursor = (int) $result[0];
                    $keys = $result[1];

                    if (!empty($keys)) {
                        $redis->command('DEL', $keys);
                    }
                } while ($cursor !== 0);

            } catch (Exception $e) {
                Log::error('Failed to invalidate Redis cache', [
                    'slug' => $slug,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Delete cache keys by pattern (for non-tagged cache stores)
     */
    private function deleteKeysByPattern(string $pattern): void
    {
        $cacheDriver = config('cache.default');

        try {
            switch ($cacheDriver) {
                case 'file':
                    $this->deleteFileCacheByPattern($pattern);
                    break;
                case 'database':
                    $this->deleteDatabaseCacheByPattern($pattern);
                    break;
                default:
                    // Для других драйверов используем Cache facade
                    $keys = Cache::getStore()->getKeys();
                    foreach ($keys as $key) {
                        if (str_contains($key, $pattern)) {
                            Cache::forget($key);
                        }
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete keys by pattern', [
                'pattern' => $pattern,
                'driver' => $cacheDriver,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete database cache by pattern
     */
    private function deleteDatabaseCacheByPattern(string $pattern): void
    {
        try {
            DB::table('cache')
                ->where('key', 'like', '%' . $pattern . '%')
                ->delete();
        } catch (\Exception $e) {
            Log::warning('Failed to delete database cache', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete file cache by pattern
     */
    private function deleteFileCacheByPattern(string $pattern): void
    {
        $cachePath = storage_path('framework/cache/data');

        if (!is_dir($cachePath)) {
            return;
        }

        $files = glob($cachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $content = file_get_contents($file);
                if ($content && str_contains($content, $pattern)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Get popular services from Redis sorted sets
     */
    public function getPopularServices(int $limit = 5): array
    {
        if (!$this->isRedisCache()) {
            // Если Redis не используется, возвращаем популярные из базы
            return Service::query()
                ->active()
                ->where('is_popular', true)
                ->orWhere('views_count', '>', 100)
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'slug' => $service->slug,
                        'name' => $service->name,
                        'short_description' => $service->short_description,
                        'price' => (float) $service->price,
                        'final_price' => (float) $service->final_price,
                        'primary_image_url' => $service->primary_image_url,
                        'thumbnail_url' => $service->thumbnail_url,
                    ];
                })->toArray();
        }

        try {
            $redis = $this->redis();

            // Get top services from weekly popularity
            $slugs = $redis->command('ZREVRANGE', ['services:popularity:weekly', 0, $limit - 1, 'WITHSCORES']);

            if (empty($slugs)) {
                return $this->getPopularServicesFromDB($limit);
            }

            // Format result as associative array [slug => score]
            $slugsWithScores = [];
            for ($i = 0; $i < count($slugs); $i += 2) {
                if (isset($slugs[$i + 1])) {
                    $slugsWithScores[$slugs[$i]] = (int) $slugs[$i + 1];
                }
            }

            // Get service details for popular slugs
            $services = Service::query()
                ->whereIn('slug', array_keys($slugsWithScores))
                ->active()
                ->get()
                ->map(function ($service) use ($slugsWithScores) {
                    $score = $slugsWithScores[$service->slug] ?? 0;
                    return [
                        'id' => $service->id,
                        'slug' => $service->slug,
                        'name' => $service->name,
                        'short_description' => $service->short_description,
                        'price' => (float) $service->price,
                        'final_price' => (float) $service->final_price,
                        'popularity_score' => (int) $score,
                        'primary_image_url' => $service->primary_image_url,
                        'thumbnail_url' => $service->thumbnail_url,
                    ];
                })->sortByDesc('popularity_score')->values()->toArray();

            return $services;

        } catch (Exception $e) {
            Log::error('Failed to get popular services', [
                'error' => $e->getMessage()
            ]);
            return $this->getPopularServicesFromDB($limit);
        }
    }

    /**
     * Get popular services from database
     */
    private function getPopularServicesFromDB(int $limit): array
    {
        try {
            return Service::query()
                ->active()
                ->where(function ($query) {
                    $query->where('is_popular', true)
                        ->orWhere('views_count', '>', 50)
                        ->orWhere('rating', '>=', 4.5);
                })
                ->orderByRaw('CASE WHEN is_popular THEN 1 ELSE 2 END')
                ->orderBy('views_count', 'desc')
                ->orderBy('rating', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'slug' => $service->slug,
                        'name' => $service->name,
                        'short_description' => $service->short_description,
                        'price' => (float) $service->price,
                        'final_price' => (float) $service->final_price,
                        'primary_image_url' => $service->primary_image_url,
                        'thumbnail_url' => $service->thumbnail_url,
                    ];
                })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get popular services fallback', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Reset popularity counters (for cron job)
     */
    public function resetPopularityCounters(string $period = 'weekly'): void
    {
        if (!$this->isRedisCache()) {
            return;
        }

        try {
            $key = sprintf('services:popularity:%s', $period);
            $this->redis()->command('DEL', [$key]);

            Log::info(sprintf('Reset %s popularity counters', $period));

        } catch (Exception $e) {
            Log::error('Failed to reset popularity counters', [
                'period' => $period,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear all service-related cache
     */
    public function clearAllServiceCache(): void
    {
        try {
            $patterns = [
                'services:*',
                'service:*',
                '*services*',
                '*service*',
            ];

            foreach ($patterns as $pattern) {
                $this->deleteKeysByPattern($pattern);
            }

            if ($this->isRedisCache()) {
                try {
                    $redis = $this->redis();
                    foreach ($patterns as $pattern) {
                        $cursor = 0;
                        do {
                            $result = $redis->command('SCAN', [$cursor, 'MATCH', $pattern, 'COUNT', 100]);
                            $cursor = (int) $result[0];
                            $keys = $result[1];

                            if (!empty($keys)) {
                                $redis->command('DEL', [$keys]);
                            }
                        } while ($cursor !== 0);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to clear Redis cache', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('All service cache cleared');

        } catch (\Exception $e) {
            Log::error('Failed to clear all service cache', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Simple cache remember with tags
     */
    public function remember(
        string $key,
        array $tags,
        int $ttl,
        callable $callback
    ) {
        $cacheKey = $this->generateKeyWithTags($key, $tags);

        if ($this->supportsTags) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        return Cache::remember($cacheKey, $ttl, $callback);
    }
}

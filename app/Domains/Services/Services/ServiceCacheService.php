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
        return sprintf('services:index:%s:%s:%s', self::CACHE_VERSION, $locale, $fingerprint);
    }

    public function generateShowKey(string $slug): string
    {
        $locale = app()->getLocale();
        return sprintf('services:show:%s:%s:%s', self::CACHE_VERSION, $locale, $slug);
    }

    public function generateFilterKey(Request $request): string
    {
        $locale = app()->getLocale();
        $fingerprint = md5(serialize($request->all()));
        return sprintf('services:filter:%s:%s:%s', self::CACHE_VERSION, $locale, $fingerprint);
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
            $cacheKey = $this->generateKeyWithTags($key, $tags);

            if ($this->supportsTags) {
                $cached = Cache::tags($tags)->get($key);
            } else {
                $cached = Cache::get($cacheKey);
            }

            if ($cached !== null) {
                return $cached;
            }

            $result = $callback();

            if ($this->supportsTags) {
                Cache::tags($tags)->put($key, $result, $ttl);
            } else {
                Cache::put($cacheKey, $result, $ttl);
            }

            if ($this->isRedisCache()) {
                $this->redis()->setex($key, $ttl, serialize($result));
            }

            return $result;
        } catch (Exception $e) {
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

        if ($value && $hasKey) {
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
            $ttl = $this->redis()->ttl($key);
            return $ttl > 0 && $ttl < 360;
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

            $redis->incr($key);
            $redis->expire($key, 86400);

            $redis->zincrby('services:popularity:weekly', 1, $slug);
            $redis->zincrby('services:popularity:monthly', 1, $slug);
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
        if ($this->supportsTags) {
            Cache::tags(['services.show', 'service.' . $slug])->flush();
        } else {
            $pattern = sprintf('*services*%s*', $slug);
            $this->deleteKeysByPattern($pattern);
        }

        $tagsToClear = ['services.index', 'services.filter', 'services.category'];
        foreach ($tagsToClear as $tag) {
            if ($this->supportsTags) {
                Cache::tags([$tag])->flush();
            } else {
                $pattern = sprintf('*%s*', str_replace(':', '_', $tag));
                $this->deleteKeysByPattern($pattern);
            }
        }

        if ($this->isRedisCache()) {
            $this->clearRedisKeysByPattern(sprintf('*services*%s*', $slug));
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
     * Clear Redis keys by pattern using proper Laravel Redis API
     */
    private function clearRedisKeysByPattern(string $pattern): void
    {
        if (!$this->isRedisCache()) {
            return;
        }

        try {
            $redis = $this->redis();

            // Используем scan итератор (правильный способ для Redis)
            $iterator = null;
            $keysToDelete = [];

            do {
                // Правильный синтаксис для Redis scan в Laravel
                $result = $redis->scan($iterator, ['match' => $pattern, 'count' => 100]);

                if (is_array($result)) {
                    $keysToDelete = array_merge($keysToDelete, $result);
                }

            } while ($iterator > 0);

            // Удаляем найденные ключи
            if (!empty($keysToDelete)) {
                $redis->del($keysToDelete);
            }

        } catch (Exception $e) {
            Log::error('Failed to clear Redis cache by pattern', [
                'pattern' => $pattern,
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
            return $this->getPopularServicesFromDB($limit);
        }

        try {
            $redis = $this->redis();

            // Используем правильный синтаксис для ZREVRANGE
            $slugs = $redis->zrevrange('services:popularity:weekly', 0, $limit - 1, 'WITHSCORES');

            if (empty($slugs)) {
                return $this->getPopularServicesFromDB($limit);
            }

            $slugsWithScores = [];
            foreach ($slugs as $slug => $score) {
                $slugsWithScores[$slug] = (int) $score;
            }

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
                })
                ->sortByDesc('popularity_score')
                ->values()
                ->toArray();

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
                })
                ->toArray();
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
            $this->redis()->del([$key]);
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
            ];

            foreach ($patterns as $pattern) {
                $this->deleteKeysByPattern($pattern);
            }

            if ($this->isRedisCache()) {
                $this->clearAllRedisServiceCache();
            }

            Log::info('All service cache cleared');
        } catch (\Exception $e) {
            Log::error('Failed to clear all service cache', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear all Redis service cache
     */
    private function clearAllRedisServiceCache(): void
    {
        try {
            $redis = $this->redis();
            $patterns = ['services:*', 'service:*'];

            foreach ($patterns as $pattern) {
                $iterator = null;
                $keysToDelete = [];

                do {
                    $result = $redis->scan($iterator, ['match' => $pattern, 'count' => 100]);

                    if (is_array($result)) {
                        $keysToDelete = array_merge($keysToDelete, $result);
                    }

                } while ($iterator > 0);

                if (!empty($keysToDelete)) {
                    $redis->del($keysToDelete);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear Redis service cache', [
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

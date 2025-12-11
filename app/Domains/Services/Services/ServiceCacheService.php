<?php

declare(strict_types=1);

namespace App\Domains\Services\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ServiceCacheService
{
    private const CACHE_VERSION = 'v2';
    private const STALE_TTL = 86400; // 24 hours for stale-while-revalidate

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
            // Try to get from cache
            if ($cached = Cache::tags($tags)->get($key)) {
                return $cached;
            }

            // Execute callback and cache result
            $result = $callback();

            // Store in cache with TTL
            Cache::tags($tags)->put($key, $result, $ttl);

            // Also store in Redis for faster access
            if (config('cache.default') === 'redis') {
                Redis::setex($key, $ttl, serialize($result));
            }

            return $result;

        } catch (\Exception $e) {
            // Log error and use fallback
            \Log::warning('Cache failure for key: ' . $key, [
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
        $value = Cache::tags($tags)->get($key);

        // Check if cache is stale but still valid
        if ($value && Cache::tags($tags)->has($key)) {
            // Refresh cache asynchronously if stale
            if ($this->isStale($key)) {
                dispatch(function () use ($key, $tags, $ttl, $callback) {
                    try {
                        $freshValue = $callback();
                        Cache::tags($tags)->put($key, $freshValue, $ttl);
                    } catch (\Exception $e) {
                        \Log::error('Background cache refresh failed', [
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
            Cache::tags($tags)->put($key, $freshValue, $ttl);
        }

        return $freshValue;
    }

    private function isStale(string $key): bool
    {
        $ttl = Redis::ttl($key);

        // Consider stale if TTL is less than 10% of original
        return $ttl > 0 && $ttl < 360; // 6 minutes for 1 hour TTL
    }

    public function incrementViewCount(string $slug): void
    {
        $key = sprintf('service:views:%s:%s', date('Y-m-d'), $slug);

        Redis::incr($key);
        Redis::expire($key, 86400); // Keep for 24 hours

        // Update weekly/monthly counters
        Redis::zincrby('services:popularity:weekly', 1, $slug);
        Redis::zincrby('services:popularity:monthly', 1, $slug);
    }

    /**
     * Invalidate cache intelligently (for admin panel webhooks)
     */
    public function invalidateService(string $slug): void
    {
        // Clear specific service cache
        Cache::tags(['services.show', 'service.' . $slug])->flush();

        // Clear related caches
        Cache::tags(['services.index'])->flush();
        Cache::tags(['services.filter'])->flush();

        // Clear Redis cache if used
        if (config('cache.default') === 'redis') {
            $pattern = sprintf('services:*%s*', $slug);
            $keys = Redis::keys($pattern);

            if (!empty($keys)) {
                Redis::del($keys);
            }
        }
    }
}

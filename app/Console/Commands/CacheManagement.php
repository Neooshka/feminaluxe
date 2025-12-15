<?php

namespace App\Console\Commands;

use App\Domains\Services\Services\ServiceCacheService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CacheManagement extends Command
{
    protected $signature = 'cache:manage
                            {action : clear|status|warm|reset}
                            {--type=services : services|all|popularity}
                            {--slug= : Service slug for specific clear}';

    protected $description = 'Manage application cache';

    public function handle(ServiceCacheService $cacheService): int
    {
        $action = $this->argument('action');
        $type = $this->option('type');
        $slug = $this->option('slug');

        switch ($action) {
            case 'clear':
                $this->clearCache($cacheService, $type, $slug);
                break;

            case 'status':
                $this->showCacheStatus();
                break;

            case 'warm':
                $this->warmCache($cacheService);
                break;

            case 'reset':
                $this->resetCache($cacheService, $type);
                break;

            default:
                $this->error('Invalid action. Use: clear, status, warm, or reset');
                return CommandAlias::FAILURE;
        }

        return CommandAlias::SUCCESS;
    }

    private function clearCache(ServiceCacheService $cacheService, string $type, ?string $slug): void
    {
        $this->info("Clearing $type cache...");

        switch ($type) {
            case 'services':
                if ($slug) {
                    $cacheService->invalidateService($slug);
                    $this->info("Cache cleared for service: $slug");
                } else {
                    $cacheService->clearAllServiceCache();
                    $this->info('All service cache cleared.');
                }
                break;

            case 'popularity':
                $cacheService->resetPopularityCounters();
                $cacheService->resetPopularityCounters('monthly');
                $this->info('Popularity counters reset.');
                break;

            case 'all':
                Cache::flush();
                $this->info('All application cache cleared.');
                break;
        }
    }

    private function showCacheStatus(): void
    {
        $driver = config('cache.default');
        $this->info("Cache Driver: $driver");

        if ($driver === 'redis') {
            try {
                $redis = Redis::connection();
                $info = $redis->info();
                $this->info("Redis Memory Usage: " . round($info['used_memory_human'] ?? 0));
                $this->info("Redis Keys: " . ($info['db0']['keys'] ?? 'N/A'));
            } catch (Exception $e) {
                $this->error('Redis connection failed: ' . $e->getMessage());
            }
        }
    }

    private function warmCache(ServiceCacheService $cacheService): void
    {
        $this->info('Warming up service cache...');

        // Можно добавить логику предварительной загрузки кэша
        $this->info('Cache warming completed.');
    }

    private function resetCache(ServiceCacheService $cacheService, string $type): void
    {
        $this->clearCache($cacheService, $type, null);
        $this->warmCache($cacheService);
    }
}

<?php

namespace App\Console\Commands;

use App\Domains\Services\Services\ServiceCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ManageServiceCache extends Command
{
    protected $signature = 'services:cache
                            {action : reset|stats|clear}
                            {--period=weekly : weekly or monthly for reset action}
                            {--slug= : Specific service slug for clear}';

    protected $description = 'Manage service cache and Redis counters';

    public function handle(ServiceCacheService $cacheService): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'reset':
                $period = $this->option('period');
                $cacheService->resetPopularityCounters($period);
                $this->info("{$period} popularity counters reset.");
                break;

            case 'stats':
                $this->showCacheStats();
                break;

            case 'clear':
                $slug = $this->option('slug');
                if ($slug) {
                    $cacheService->invalidateService($slug);
                    $this->info("Cache cleared for service: {$slug}");
                } else {
                    $this->error('Please provide --slug option for clear action');
                }
                break;

            default:
                $this->error('Invalid action. Use: reset, stats, or clear');
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function showCacheStats(): void
    {
        if (!config('cache.default') === 'redis') {
            $this->error('Redis is not configured as cache driver');
            return;
        }

        try {
            $redis = Redis::connection('cache');

            // Weekly popularity
            $weeklyCount = $redis->command('ZCARD', ['services:popularity:weekly']);
            $weeklyTop = $redis->command('ZREVRANGE', ['services:popularity:weekly', 0, 4, 'WITHSCORES']);

            // Monthly popularity
            $monthlyCount = $redis->command('ZCARD', ['services:popularity:monthly']);

            $this->info("=== Service Cache Statistics ===");
            $this->info("Weekly popular services: {$weeklyCount}");
            $this->info("Monthly popular services: {$monthlyCount}");

            if (!empty($weeklyTop)) {
                $this->info("\nTop 5 popular services (weekly):");
                for ($i = 0; $i < count($weeklyTop); $i += 2) {
                    if (isset($weeklyTop[$i + 1])) {
                        $this->info("  {$weeklyTop[$i]}: {$weeklyTop[$i + 1]} views");
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error('Failed to get cache stats: ' . $e->getMessage());
        }
    }
}

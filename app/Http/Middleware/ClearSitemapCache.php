<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ClearSitemapCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Если были изменения в моделях, которые влияют на sitemap
        if ($this->shouldClearSitemapCache($request)) {
            cache()->forget('sitemap.generated');

            // Можно также запланировать генерацию
            Artisan::queue('sitemap:generate');
        }

        return $response;
    }

    private function shouldClearSitemapCache(Request $request): bool
    {
        $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $paths = [
            'admin/posts',
            'admin/pages',
            'admin/categories',
            'admin/products',
        ];

        return in_array($request->method(), $methods) &&
               $request->is($paths);
    }
}

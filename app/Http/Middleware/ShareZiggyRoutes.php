<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tighten\Ziggy\Ziggy;

class ShareZiggyRoutes
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Добавляем маршруты в каждый Inertia ответ
        $response = $next($request);

        if ($response instanceof \Inertia\Response) {
            $response->with([
                'ziggy' => (new Ziggy('web'))->toArray(),
            ]);
        }

        return $response;
    }
}

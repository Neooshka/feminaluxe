<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => array_merge(
                (new Ziggy)->toArray(),
                ['location' => $request->url()]
            ),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            // Optional: Share seo array for Vue Head if client-side dynamic needed (e.g., SPA navigation)
            'seo' => fn () => $request->get('SEOData')?->toArray() ?? [],  // From controller withViewData
        ]);
    }
}

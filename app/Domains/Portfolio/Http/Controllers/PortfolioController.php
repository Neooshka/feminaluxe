<?php

declare(strict_types=1);

namespace App\Domains\Portfolio\Http\Controllers;

use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Portfolio\Services\PortfolioSeoService;
use App\Domains\Reviews\Models\Review;
use App\Domains\Services\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController
{
    public function __construct(
        private PortfolioSeoService $seoService
    ) {}

    // app/Domains/Portfolio/Http/Controllers/PortfolioController.php
    public function index(Request $request): Response
    {
        $query = Portfolio::published()
            ->with(['service', 'media'])
            ->orderBy('sort_order')
            ->orderBy('published_at', 'desc');

        // Фильтр по услуге
        if ($request->has('service') && $request->service) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('slug', $request->service);
            });
        }

        // Фильтр по категории
        if ($request->has('category') && $request->category) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $portfolios = $query->paginate(12)
            ->through(function ($portfolio) {
                return [
                    'id' => $portfolio->id,
                    'title' => $portfolio->title,
                    'slug' => $portfolio->slug,
                    'description' => Str::limit(strip_tags($portfolio->description), 120),
                    'service_name' => $portfolio->service->name,
                    'service_slug' => $portfolio->service->slug,
                    'before_url' => $portfolio->getFirstMediaUrl('before') ?:
                        asset('img/placeholder-before.jpg'),
                    'after_url' => $portfolio->getFirstMediaUrl('after') ?:
                        asset('img/placeholder-after.jpg'),
                    'views_count' => $portfolio->views_count,
                    'published_at' => $portfolio->published_at->format('d.m.Y'),
                ];
            });

        // Статистика рейтинга
        $avgRating = (float) Review::approved()->avg('rating') ?? 0;
        $averageRating = round($avgRating, 1);

        // Список услуг для фильтра
        $services = Service::query()
            ->active()
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'slug', 'name', 'category', 'price', 'discounted_price', 'duration', 'has_promotion'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'slug' => $service->slug,
                    'name' => $service->name,
                    'category' => $service->category,
                    'price' => $service->price,
                    'final_price' => $service->has_promotion ? $service->discounted_price : $service->price,
                    'duration' => $service->duration,
                    'has_promotion' => $service->has_promotion,
                ];
            })
            ->toArray();

        return Inertia::render('Portfolio/Index', [
            'portfolios' => $portfolios,
            'filters' => $request->only(['service', 'category']),
            'averageRating' => $averageRating,
            'services' => $services,
        ])->withViewData([
            'SEOData' => $this->seoService->generateIndexSeoData()
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $portfolio = Portfolio::published()
            ->where('slug', $slug)
            ->with(['service', 'media'])
            ->firstOrFail();

        // Увеличиваем счетчик просмотров
        $portfolio->incrementViews();

        $related = Portfolio::published()
            ->where('service_id', $portfolio->service_id)
            ->where('id', '!=', $portfolio->id)
            ->limit(4)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'before_url' => $item->getFirstMediaUrl('before'),
                    'service_name' => $item->service->name,
                ];
            });

        return Inertia::render('Portfolio/Show', [
            'portfolio' => [
                'id' => $portfolio->id,
                'title' => $portfolio->title,
                'description' => $portfolio->description,
                'service' => [
                    'id' => $portfolio->service->id,
                    'name' => $portfolio->service->name,
                    'slug' => $portfolio->service->slug,
                ],
                'before_url' => $portfolio->getFirstMediaUrl('before'),
                'after_url' => $portfolio->getFirstMediaUrl('after'),
                'gallery' => $portfolio->getMedia('gallery')->map(fn($media) => [
                    'url' => $media->getUrl(),
                    'thumb' => route('media.thumbnail', [
                        'mediaId' => $media->id,
                        'type' => 'thumb'
                    ])
                ]),
                'views_count' => $portfolio->views_count,
                'published_at' => $portfolio->published_at->format('d.m.Y'),
            ],
            'related' => $related,
        ])->withViewData([
            'SEOData' => $portfolio->getDynamicSEOData(),
        ]);
    }
}
<?php

declare(strict_types=1);

namespace App\Domains\Services\Http\Controllers;

use App\Domains\Services\Data\ServiceShowData;
use App\Domains\Services\Data\ServiceIndexData;
use App\Domains\Services\Data\TransformedService;
use App\Domains\Services\Models\Service;
use App\Domains\Services\Services\ServiceCacheService;
use App\Domains\Services\Services\ServiceSeoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\DataCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceCacheService $cacheService,
        private readonly ServiceSeoService $seoService,
    ) {
        // Apply rate limiting for production environments
        $this->middleware('throttle:60,1')->only(['index', 'show']);
    }

    /**
     * Display services index with optimized caching strategy
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Use request-aware caching with versioning for cache invalidation
        $cacheKey = $this->cacheService->generateIndexKey($request);

        /** @var DataCollection<int, TransformedService> $services */
        $services = $this->cacheService->rememberWithFallback(
            $cacheKey,
            ['services.index'],
            3600, // 1 hour TTL
            function () use ($request) {
                return ServiceIndexData::from(
                    Service::query()
                        ->with(['media', 'reviews' => fn($query) => $query->where('approved', true)])
                        ->active()
                        ->orderBy('category')
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get()
                )->services;
            },
            function () use ($request) {
                // Fallback: Return minimal data if cache fails
                return ServiceIndexData::from(
                    Service::query()
                        ->active()
                        ->orderBy('category')
                        ->get()
                )->services;
            }
        );

        return Inertia::render('Services/Index', [
            'services' => $services->toArray(),
            'categories' => $services->unique('category')->pluck('category')->toArray(),
            'meta' => [
                'title' => $this->seoService->getIndexTitle(),
                'description' => $this->seoService->getIndexDescription(),
                'keywords' => $this->seoService->getIndexKeywords(),
            ]
        ])->withViewData([
            'SEOData' => $this->seoService->generateIndexSeoData()
        ]);
    }

    /**
     * Display single service with comprehensive data and edge caching
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function show(Request $request, string $slug): Response
    {
        // Validate slug format before querying database
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            abort(404);
        }

        $cacheKey = $this->cacheService->generateShowKey($slug);

        /** @var ServiceShowData|null $serviceData */
        $serviceData = $this->cacheService->rememberWithStaleWhileRevalidate(
            $cacheKey,
            ['services.show', 'service.' . $slug],
            7200, // 2 hours TTL
            function () use ($slug) {
                $service = Service::query()
                    ->with([
                        'media',
                        'reviews' => fn($query) => $query
                            ->where('approved', true)
                            ->orderBy('created_at', 'desc')
                            ->limit(10),
                        'faqs' => fn($query) => $query->where('active', true),
                        'relatedServices' => fn($query) => $query->active(),
                    ])
                    ->where('slug', $slug)
                    ->active()
                    ->first();

                if (!$service) {
                    return null;
                }

                return ServiceShowData::from($service);
            }
        );

        if (!$serviceData) {
            throw new NotFoundHttpException('Service not found');
        }

        // Track view for popularity-based recommendations
        $this->cacheService->incrementViewCount($slug);

        // Generate structured data for rich snippets
        $structuredData = $this->seoService->generateServiceStructuredData($serviceData);

        return Inertia::render('Services/Show', [
            'service' => $serviceData->toArray(),
            'relatedServices' => $serviceData->relatedServices->toArray(),
            'structuredData' => $structuredData,
            'meta' => [
                'title' => $this->seoService->getShowTitle($serviceData),
                'description' => $this->seoService->getShowDescription($serviceData),
                'canonical' => route('services.show', $slug),
            ]
        ])->withViewData([
            'SEOData' => $this->seoService->generateShowSeoData($serviceData),
            'structuredData' => json_encode($structuredData),
        ]);
    }

    /**
     * API endpoint for AJAX filtering (with JSON:API specification)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|max:50',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort_by' => 'nullable|in:price_asc,price_desc,popularity,rating',
        ]);

        $cacheKey = $this->cacheService->generateFilterKey($request);

        $services = $this->cacheService->remember(
            $cacheKey,
            ['services.filter'],
            300, // 5 minutes TTL for filtered results
            function () use ($request) {
                $query = Service::query()
                    ->with(['media'])
                    ->active();

                // Apply filters
                if ($category = $request->get('category')) {
                    $query->where('category', $category);
                }

                if ($minPrice = $request->get('min_price')) {
                    $query->where('price', '>=', $minPrice);
                }

                if ($maxPrice = $request->get('max_price')) {
                    $query->where('price', '<=', $maxPrice);
                }

                // Apply sorting
                match ($request->get('sort_by', 'price_asc')) {
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'popularity' => $query->orderBy('views_count', 'desc'),
                    'rating' => $query->orderBy('rating', 'desc'),
                    default => $query->orderBy('price', 'asc'),
                };

                return ServiceIndexData::from($query->get())->services;
            }
        );

        return response()->json([
            'data' => $services->toArray(),
            'meta' => [
                'total' => $services->count(),
                'filters' => $request->all(),
            ],
            'links' => [
                'self' => $request->fullUrl(),
            ]
        ], 200, [
            'Cache-Control' => 'public, max-age=300, stale-while-revalidate=86400',
            'Vary' => 'Accept, X-Requested-With',
        ]);
    }
}

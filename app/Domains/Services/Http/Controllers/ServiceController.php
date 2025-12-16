<?php

declare(strict_types=1);

namespace App\Domains\Services\Http\Controllers;

use App\Domains\Services\Models\Service;
use App\Domains\Services\Services\ServiceCacheService;
use App\Domains\Services\Services\ServiceSeoService;
use App\Domains\Services\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceCacheService $cacheService,
        private readonly ServiceSeoService   $seoService,
        private readonly CategoryService   $categoryService,
    )
    {
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

        $services = $this->cacheService->rememberWithFallback(
            $cacheKey,
            ['services.index'],
            3600, // 1 hour TTL
            function () use ($request) {
                return Service::query()
                    ->with(['media', 'reviews' => fn($query) => $query->where('approved', true)])
                    ->active()
                    ->orderBy('category')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'category' => $service->category ?? 'Другое',
                            'name' => $service->name,
                            'short_description' => $service->short_description,
                            'description' => $service->description,
                            'price' => (float) $service->price,
                            'discounted_price' => $service->discounted_price ? (float) $service->discounted_price : null,
                            'final_price' => (float) $service->final_price,
                            'discount_percentage' => $service->discount_percentage,
                            'duration' => $service->duration,
                            'rating' => (float) $service->rating,
                            'reviews_count' => $service->reviews_count,
                            'is_popular' => (bool) $service->is_popular,
                            'has_promotion' => (bool) $service->has_promotion,
                            'is_on_sale' => (bool) $service->is_on_sale,
                            'primary_image_url' => $service->primary_image_url,
                            'thumbnail_url' => $service->thumbnail_url,
                            'images' => $service->getMedia('before_after')->map(fn($media) => [
                                'thumb' => $media->getUrl('thumb'),
                                'full' => $media->getUrl(),
                            ])->toArray(),
                        ];
                    })->toArray();
            },
            function () use ($request) {
                // Fallback: Return minimal data if cache fails
                return Service::query()
                    ->active()
                    ->orderBy('category')
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'category' => $service->category ?? 'Другое',
                            'name' => $service->name,
                            'price' => (float) $service->price,
                            'final_price' => (float) $service->final_price,
                            'primary_image_url' => $service->primary_image_url,
                        ];
                    })->toArray();
            }
        );

        // Get popular services for recommendations
        $popularServices = $this->cacheService->getPopularServices(3);

        return Inertia::render('Services/Index', [
            'services' => $services,
            'categories' => $this->categoryService->getCategoriesWithCounts(),
            'popular_services' => $popularServices,
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
                    ])
                    ->where('slug', $slug)
                    ->active()
                    ->first();

                if (!$service) {
                    return null;
                }

                // Get related services (same category, exclude current)
                $relatedServices = Service::query()
                    ->where('category', $service->category)
                    ->where('id', '!=', $service->id)
                    ->active()
                    ->orderBy('sort_order')
                    ->limit(3)
                    ->get()
                    ->map(function ($relatedService) {
                        return [
                            'id' => $relatedService->id,
                            'slug' => $relatedService->slug,
                            'name' => $relatedService->name,
                            'short_description' => $relatedService->short_description,
                            'price' => (float) $relatedService->price,
                            'final_price' => (float) $relatedService->final_price,
                            'discount_percentage' => $relatedService->discount_percentage,
                            'primary_image_url' => $relatedService->primary_image_url,
                            'thumbnail_url' => $relatedService->thumbnail_url,
                        ];
                    })->toArray();

                return [
                    'id' => $service->id,
                    'slug' => $service->slug,
                    'category' => $service->category ?? 'Другое',
                    'name' => $service->name,
                    'short_description' => $service->short_description,
                    'description' => $service->description,
                    'detailed_description' => $service->detailed_description,
                    'price' => (float) $service->price,
                    'discounted_price' => $service->discounted_price ? (float) $service->discounted_price : null,
                    'final_price' => (float) $service->final_price,
                    'discount_percentage' => $service->discount_percentage,
                    'duration' => $service->duration,
                    'rating' => (float) $service->rating,
                    'reviews_count' => $service->reviews_count,
                    'views_count' => $service->views_count,
                    'is_popular' => (bool) $service->is_popular,
                    'has_promotion' => (bool) $service->has_promotion,
                    'is_on_sale' => (bool) $service->is_on_sale,
                    'benefits' => $service->benefits ?? [],
                    'contraindications' => $service->contraindications ?? [],
                    'primary_image_url' => $service->primary_image_url,
                    'thumbnail_url' => $service->thumbnail_url,
                    'images' => $service->getMedia('before_after')->map(fn($media) => [
                        'thumb' => $media->getUrl('thumb'),
                        'full' => $media->getUrl(),
                        'medium' => $media->getUrl('medium'),
                    ])->toArray(),
                    'gallery_images' => $service->getMedia('gallery')->map(fn($media) => [
                        'original' => $media->getUrl(),
                        'thumbnail' => $media->getUrl('thumb'),
                        'medium' => $media->getUrl('medium'),
                        'alt' => $service->name,
                    ])->toArray(),
                    'reviews' => $service->reviews->map(fn($review) => [
                        'id' => $review->id,
                        'author' => $review->author,
                        'content' => $review->content,
                        'rating' => (float) $review->rating,
                        'created_at' => $review->created_at->format('d.m.Y'),
                    ])->toArray(),
                    'related_services' => $relatedServices,
                    'created_at' => $service->created_at->format('d.m.Y'),
                    'updated_at' => $service->updated_at->format('d.m.Y'),
                ];
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
            'service' => $serviceData,
            'structuredData' => $structuredData,
            'meta' => [
                'title' => $this->seoService->getShowTitle($serviceData),
                'description' => $this->seoService->getShowDescription($serviceData),
                'canonical' => route('services.show', $slug),
            ]
        ])->withViewData([
            'SEOData' => $this->seoService->generateShowSeoData($serviceData),
        ]);
    }

    /**
     * API endpoint for AJAX filtering (with JSON:API specification)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filter(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|string|max:50',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort_by' => 'nullable|in:price_asc,price_desc,popularity,rating,newest',
        ]);

        $cacheKey = $this->cacheService->generateFilterKey($request);

        $services = $this->cacheService->rememberWithFallback(
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
                $sortBy = $request->get('sort_by', 'price_asc');
                switch ($sortBy) {
                    case 'price_desc':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'popularity':
                        $query->orderBy('views_count', 'desc');
                        break;
                    case 'rating':
                        $query->orderBy('rating', 'desc');
                        break;
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    default: // price_asc
                        $query->orderBy('price', 'asc');
                        break;
                }

                // Default secondary sorting
                $query->orderBy('sort_order')->orderBy('name');

                return $query->get()->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'slug' => $service->slug,
                        'category' => $service->category ?? 'Другое',
                        'name' => $service->name,
                        'short_description' => $service->short_description,
                        'price' => (float) $service->price,
                        'discounted_price' => $service->discounted_price ? (float) $service->discounted_price : null,
                        'final_price' => (float) $service->final_price,
                        'discount_percentage' => $service->discount_percentage,
                        'duration' => $service->duration,
                        'rating' => (float) $service->rating,
                        'reviews_count' => $service->reviews_count,
                        'is_popular' => (bool) $service->is_popular,
                        'has_promotion' => (bool) $service->has_promotion,
                        'is_on_sale' => (bool) $service->is_on_sale,
                        'primary_image_url' => $service->primary_image_url,
                        'thumbnail_url' => $service->thumbnail_url,
                        'images' => $service->getMedia('before_after')->take(1)->map(fn($media) => [
                            'thumb' => $media->getUrl('thumb'),
                            'full' => $media->getUrl(),
                        ])->first(),
                    ];
                })->toArray();
            },
            function () use ($request) {
                // Fallback for filter - simple query
                return Service::query()
                    ->active()
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'name' => $service->name,
                            'price' => (float) $service->price,
                            'final_price' => (float) $service->final_price,
                        ];
                    })->toArray();
            }
        );

        return response()->json([
            'data' => $services,
            'meta' => [
                'total' => $services->count(),
                'filters' => $request->all(),
                'sort_by' => $request->get('sort_by', 'price_asc'),
            ],
            'links' => [
                'self' => $request->fullUrl(),
            ]
        ], 200, [
            'Cache-Control' => 'public, max-age=300, stale-while-revalidate=86400',
            'Vary' => 'Accept, X-Requested-With',
        ]);
    }

    /**
     * Get services by category
     *
     * @param Request $request
     * @param string $category
     * @return Response
     */
    public function category(Request $request, string $category): Response
    {
        // Validate category format
        if (!preg_match('/^[a-zа-яё0-9\-]+$/iu', $category)) {
            abort(404);
        }

        // Convert URL slug to category name (e.g., "permanent-brovey" -> "Перманент бровей")
        $categoryName = $this->normalizeCategoryName($category);

        $cacheKey = $this->cacheService->generateIndexKey($request) . ':category:' . $category;

        $services = $this->cacheService->rememberWithFallback(
            $cacheKey,
            ['services.category', 'category.' . $categoryName],
            1800, // 30 minutes TTL
            function () use ($categoryName) {
                return Service::query()
                    ->with(['media'])
                    ->where('category', $categoryName)
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'name' => $service->name,
                            'short_description' => $service->short_description,
                            'price' => $service->price,
                            'final_price' => $service->final_price,
                            'discount_percentage' => $service->discount_percentage,
                            'duration' => $service->duration,
                            'rating' => $service->rating,
                            'primary_image_url' => $service->primary_image_url,
                            'thumbnail_url' => $service->thumbnail_url,
                        ];
                    })->toArray();
            },
            function () use ($categoryName) {
                return Service::query()
                    ->where('category', $categoryName)
                    ->active()
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'name' => $service->name,
                            'price' => (float) $service->price,
                            'final_price' => (float) $service->final_price,
                            'primary_image_url' => $service->primary_image_url,
                        ];
                    })->toArray();
            }
        );

        if ($services->isEmpty()) {
            abort(404);
        }

        $categorySEO = $this->generateCategorySeoData($categoryName, $services);

        return Inertia::render('Services/Category', [
            'services' => $services,
            'category' => $categoryName,
            'category_slug' => $category,
            'meta' => [
                'title' => $categorySEO['title'],
                'description' => $categorySEO['description'],
            ]
        ])->withViewData([
            'SEOData' => $categorySEO['seo_data'],
        ]);
    }

    /**
     * Get popular services for homepage widget
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 12);

        $cacheKey = "services:popular:$limit";

        $services = $this->cacheService->rememberWithFallback(
            $cacheKey,
            ['services.popular'],
            1800, // 30 minutes TTL
            function () use ($limit) {
                return Service::query()
                    ->with(['media'])
                    ->active()
                    ->where(function ($query) {
                        $query->where('is_popular', true)
                            ->orWhere('has_promotion', true);
                    })
                    ->orderBy('sort_order')
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
                            'discount_percentage' => $service->discount_percentage,
                            'primary_image_url' => $service->primary_image_url,
                            'thumbnail_url' => $service->thumbnail_url,
                        ];
                    })->toArray();
            },
            function () use ($limit) {
                // Fallback
                return Service::query()
                    ->active()
                    ->orderBy('sort_order')
                    ->limit($limit)
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'name' => $service->name,
                            'price' => (float) $service->price,
                            'final_price' => (float) $service->final_price,
                        ];
                    })->toArray();
            }
        );

        return response()->json([
            'data' => $services,
            'meta' => [
                'total' => $services->count(),
                'limit' => $limit,
            ]
        ]);
    }

    /**
     * Normalize category name from URL slug
     *
     * @param string $categorySlug
     * @return string
     */
    private function normalizeCategoryName(string $categorySlug): string
    {
        $mapping = [
            'brovi' => 'Брови',
            'permanent-brovey' => 'Перманент бровей',
            'pudrovye-brovi' => 'Пудровые брови',
            'guby' => 'Губы',
            'akvarelnye-guby' => 'Акварельные губы',
            'mezhresnichka' => 'Межресничка',
            'mezhresnichnyj-permanent' => 'Межресничный перманент',
            'korrektsiya' => 'Коррекция',
            'udalenie' => 'Удаление',
        ];

        return $mapping[$categorySlug] ?? ucfirst(str_replace('-', ' ', $categorySlug));
    }

    /**
     * Generate SEO data for category page
     *
     * @param string $category
     * @param array $services
     * @return array
     */
    private function generateCategorySeoData(string $category, array $services): array
    {
        $title = "{$category} - Услуги перманентного макияжа в Москве | Feminaluxe";

        // Find minimum price
        $minPrice = min(array_column($services, 'final_price'));

        $description = "✨ Профессиональный {$category} в салоне Feminaluxe. " .
            "Цены от {$minPrice} руб. " .
            "Запишитесь онлайн на бесплатную консультацию!";

        $seoData = $this->seoService->generateSeoData(
            title: $title,
            description: $description,
            image: asset('img/seo/category-' . strtolower(str_replace(' ', '-', $category)) . '.jpg'),
            canonical: route('services.category', strtolower(str_replace(' ', '-', $category))),
        );

        return [
            'title' => $title,
            'description' => $description,
            'seo_data' => $seoData,
        ];
    }
}

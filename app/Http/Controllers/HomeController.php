<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Reviews\Models\Review;
use App\Domains\Services\Models\Service;
use App\Domains\Services\Services\ServiceCacheService;
use App\Domains\Services\Services\CategoryService;
use App\Helpers\ContentManager;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        private readonly ServiceCacheService $cacheService,
        private readonly CategoryService $categoryService,
    ) {}

    public function index(Request $request): Response
    {
        // Кэширование главной страницы
        $cacheKey = $this->cacheService->generateIndexKey($request);

        $data = $this->cacheService->rememberWithFallback(
            $cacheKey,
            ['home_page'],
            1800, // 30 минут
            function () {
                // Получаем услуги с ограничением для главной
                $services = Service::query()
                    ->with(['media'])
                    ->active()
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc')
                    ->limit(12)
                    ->get()
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'category' => $service->category,
                            'name' => $service->name,
                            'short_description' => $service->short_description,
                            'description' => $service->description,
                            'price' => (float) $service->price,
                            'discounted_price' => $service->discounted_price ? (float) $service->discounted_price : null,
                            'duration' => $service->duration,
                            'rating' => (float) $service->rating,
                            'reviews_count' => $service->reviews_count,
                            'views_count' => $service->views_count,
                            'is_popular' => (bool) $service->is_popular,
                            'has_promotion' => (bool) $service->has_promotion,
                            'primary_image_url' => $service->primary_image_url,
                            'thumbnail_url' => $service->thumbnail_url,
                        ];
                    })->toArray();

                // Популярные услуги
                $popularServices = $this->cacheService->getPopularServices(3);

                // Получаем все уникальные категории из базы
                $categories = $this->categoryService->getCategoriesWithCounts();
                
                // Отзывы
                $reviews = Review::with('service')->approved()->where('rating', '>=', 4)->limit(5)->get();

                // Получаем фотки мастера
                $master = ContentManager::getMasterData();

                return [
                    'reviews' => $reviews,
                    'services' => $services,
                    'popularServices' => $popularServices,
                    'categories' => $categories,
                    'master' => $master,
                ];
            },
            function () {
                // Fallback данные
                return [
                    'reviews' => [],
                    'services' => [],
                    'popularServices' => [],
                    'categories' => [],
                    'master' => ContentManager::getMasterData(),
                ];
            }
        );

        return Inertia::render('Home', $data);
    }
}

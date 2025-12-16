<?php

namespace App\Domains\Services\Services;

use App\Domains\Services\Models\Service;
use App\Domains\Services\Filters\ServiceFilter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceQueryService
{
    /**
     * Получает похожие услуги
     */
    public function getSimilarServices(Service $service, int $limit = 4): array
    {
        $cacheKey = "similar_services_{$service->id}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($service, $limit) {
            // Ищем по категории
            $similar = Service::query()
                ->where('id', '!=', $service->id)
                ->where('category_id', $service->category_id)
                ->where('is_published', true)
                ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
                ->inRandomOrder()
                ->limit($limit)
                ->get();

            // Если не набрали нужное количество, ищем по тегам
            if ($similar->count() < $limit && $service->tags->isNotEmpty()) {
                $tagServices = Service::query()
                    ->where('id', '!=', $service->id)
                    ->whereNotIn('id', $similar->pluck('id'))
                    ->where('is_published', true)
                    ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
                    ->withAnyTags($service->tags->pluck('name')->toArray())
                    ->inRandomOrder()
                    ->limit($limit - $similar->count())
                    ->get();

                $similar = $similar->merge($tagServices);
            }

            // Если все еще мало, берем популярные услуги
            if ($similar->count() < $limit) {
                $popularServices = Service::query()
                    ->where('id', '!=', $service->id)
                    ->whereNotIn('id', $similar->pluck('id'))
                    ->where('is_published', true)
                    ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
                    ->orderBy('views_count', 'desc')
                    ->limit($limit - $similar->count())
                    ->get();

                $similar = $similar->merge($popularServices);
            }

            return $similar->all();
        });
    }

    /**
     * Получает рекомендуемые услуги для пользователя
     */
    public function getRecommendedServices(?int $userId = null, int $limit = 6): array
    {
        $cacheKey = "recommended_services_{$userId}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($userId, $limit) {
            $query = Service::query()
                ->where('is_published', true)
                ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
                ->where('is_featured', true);

            // Если есть пользователь, учитываем его историю
            if ($userId) {
                $userCategories = DB::table('bookings')
                    ->join('services', 'bookings.service_id', '=', 'services.id')
                    ->where('bookings.user_id', $userId)
                    ->groupBy('services.category_id')
                    ->pluck('services.category_id');

                if ($userCategories->isNotEmpty()) {
                    $query->whereIn('category_id', $userCategories)
                        ->orWhere('is_featured', true);
                }
            }

            return $query->inRandomOrder()
                ->limit($limit)
                ->get()
                ->all();
        });
    }

    /**
     * Получает услуги со скидкой
     */
    public function getDiscountedServices(int $limit = 8): array
    {
        $cacheKey = "discounted_services_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($limit) {
            return Service::query()
                ->where('is_published', true)
                ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
                ->where(function ($q) {
                    $q->where('discounted_price', '>', 0)
                      ->orWhere('has_promotion', true);
                })
                ->where('special_offer_until', '>', now())
                ->orderBy('special_offer_until')
                ->limit($limit)
                ->get()
                ->all();
        });
    }

    /**
     * Получает популярные услуги
     */
    public function getPopularServices(int $limit = 6): array
    {
        $cacheKey = "popular_services_{$limit}_" . date('Y-m-d');

        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return Service::query()
                ->where('is_published', true)
                ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
                ->withCount(['bookings' => function ($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                }])
                ->orderBy('bookings_count', 'desc')
                ->limit($limit)
                ->get()
                ->all();
        });
    }

    /**
     * Получает статистику по услугам для главной страницы
     */
    public function getHomepageServices(): array
    {
        $cacheKey = 'homepage_services_' . date('Y-m-d');

        return Cache::remember($cacheKey, 1800, function () {
            return [
                'featured' => $this->getFeaturedServices(3),
                'discounted' => $this->getDiscountedServices(4),
                'popular' => $this->getPopularServices(6),
                'new' => $this->getNewServices(4),
            ];
        });
    }

    /**
     * Получает новые услуги
     */
    private function getNewServices(int $limit): array
    {
        return Service::query()
            ->where('is_published', true)
            ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * Получает избранные услуги
     */
    private function getFeaturedServices(int $limit): array
    {
        return Service::query()
            ->where('is_published', true)
            ->where('status', \App\Domains\Services\Enums\ServiceStatus::ACTIVE)
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->limit($limit)
            ->get()
            ->all();
    }
}

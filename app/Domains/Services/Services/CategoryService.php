<?php

declare(strict_types=1);

namespace App\Domains\Services\Services;

use App\Domains\Services\Models\Service;
use Illuminate\Support\Collection;

class CategoryService
{
    /**
     * Получить все категории с подсчетом услуг
     */
    public function getCategoriesWithCounts(): Collection
    {
        return Service::query()
            ->selectRaw('category, COUNT(*) as services_count')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->active()
            ->groupBy('category')
            ->orderBy('services_count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->category,
                    'name' => $this->getDisplayName($item->category),
                    'slug' => $this->generateSlug($item->category),
                    'count' => $item->services_count,
                    'icon' => $this->getIcon($item->category),
                    'description' => $this->getDescription($item->category),
                ];
            });
    }

    /**
     * Получить популярные категории
     */
    public function getPopularCategories(int $limit = 5): Collection
    {
        return Service::query()
            ->selectRaw('category, COUNT(*) as services_count, SUM(views_count) as total_views')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->active()
            ->groupBy('category')
            ->orderBy('total_views', 'desc')
            ->orderBy('services_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'display_name' => $this->getDisplayName($item->category),
                    'services_count' => $item->services_count,
                    'total_views' => $item->total_views,
                ];
            });
    }

    /**
     * Получить услуги по категории
     */
    public function getServicesByCategory(string $category, int $limit = null): Collection
    {
        $query = Service::query()
            ->with(['media'])
            ->where('category', $category)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    private function getDisplayName(string $category): string
    {
        $names = [
            'Брови' => 'Брови',
            'Губы' => 'Губы',
            'Межресничка' => 'Межресничка',
            'Коррекция' => 'Коррекция',
            'Удаление' => 'Удаление',
            'Пудровые брови' => 'Пудровые брови',
            'Акварельные губы' => 'Акварельные губы',
            'Перманент бровей' => 'Перманент бровей',
            'Другое' => 'Другое',
        ];

        return $names[$category] ?? $category;
    }

    private function generateSlug(string $category): string
    {
        $slugMap = [
            'Брови' => 'brovi',
            'Губы' => 'guby',
            'Межресничка' => 'mezhresnichka',
            'Коррекция' => 'korrektsiya',
            'Удаление' => 'udalenie',
            'Пудровые брови' => 'pudrovye-brovi',
            'Акварельные губы' => 'akvarelnye-guby',
            'Перманент бровей' => 'permanent-brovey',
            'Другое' => 'drugoe',
        ];

        return $slugMap[$category] ??
            mb_strtolower(preg_replace('/[^a-zа-яё0-9]+/iu', '-', $category));
    }

    private function getIcon(string $category): string
    {
        $icons = [
            'Брови' => '👁️',
            'Губы' => '💋',
            'Межресничка' => '✨',
            'Коррекция' => '🔄',
            'Удаление' => '❌',
            'Пудровые брови' => '💫',
            'Акварельные губы' => '🎨',
            'Другое' => '⭐',
        ];

        return $icons[$category] ?? '🎀';
    }

    private function getDescription(string $category): string
    {
        $descriptions = [
            'Брови' => 'Идеальная форма и цвет бровей',
            'Губы' => 'Естественный контур и объем губ',
            'Межресничка' => 'Выразительный взгляд без подводки',
            'Коррекция' => 'Исправление и обновление татуажа',
            'Удаление' => 'Безопасное удаление татуажа',
            'Пудровые брови' => 'Эффект мягкого напыления',
            'Акварельные губы' => 'Нежный натуральный оттенок',
            'Другое' => 'Различные услуги перманентного макияжа',
        ];

        return $descriptions[$category] ?? 'Профессиональная услуга';
    }
}

<?php

namespace App\Domains\Services\Filters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Domains\Services\Models\Service;
use App\Domains\Services\Enums\ServiceStatus;
use App\Domains\Services\Enums\ServiceCategory;
use Spatie\LaravelData\Data;

class ServiceFilter extends Data
{
    public function __construct(
        public ?string $category = null,
        public ?string $price_range = null,
        public ?string $sort_by = 'popularity',
        public ?string $search = null,
        public ?string $view = 'grid',
        public ?int $per_page = 12,
        public ?bool $only_active = true,
        public ?array $tags = [],
        public ?float $min_rating = null,
        public ?bool $with_discount = false
    ) {}

    /**
     * Создает экземпляр фильтра из HTTP запроса
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            category: $request->get('category'),
            price_range: $request->get('price_range'),
            sort_by: $request->get('sort_by', 'popularity'),
            search: $request->get('search'),
            view: $request->get('view', 'grid'),
            per_page: $request->get('per_page', 12),
            only_active: $request->boolean('only_active', true),
            tags: $request->has('tags') ? explode(',', $request->get('tags')) : [],
            min_rating: $request->get('min_rating'),
            with_discount: $request->boolean('with_discount')
        );
    }

    /**
     * Применяет фильтры к запросу с использованием паттерна Fluent Builder
     */
    public function apply(Builder $query): Builder
    {
        // Начинаем с базового запроса
        $this->applyBaseConditions($query);

        // Применяем фильтры в порядке оптимизации запросов
        $this->applySearchFilter($query);
        $this->applyCategoryFilter($query);
        $this->applyPriceFilter($query);
        $this->applyRatingFilter($query);
        $this->applyTagsFilter($query);
        $this->applyDiscountFilter($query);

        // Применяем сортировку в конце
        $this->applySorting($query);

        // Добавляем eager loading для оптимизации
        $this->withRelations($query);

        return $query;
    }

    /**
     * Базовые условия (активность, публичность)
     */
    private function applyBaseConditions(Builder $query): void
    {
        if ($this->only_active) {
            $query->where('is_active', true);
        }
    }

    /**
     * Фильтр по поисковому запросу с использованием полнотекстового поиска
     */
    private function applySearchFilter(Builder $query): void
    {
        if (!$this->search) {
            return;
        }

        $searchTerm = trim($this->search);

        // Если есть полнотекстовый индекс, используем его
        if (config('database.default') === 'mysql') {
            $query->where(function ($q) use ($searchTerm) {
                $q->whereFullText(['name', 'description', 'keywords'], $searchTerm)
                  ->orWhere('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%");
            });
        } else {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%");
            });
        }
    }

    /**
     * Фильтр по категории с поддержкой вложенных категорий
     */
    private function applyCategoryFilter(Builder $query): void
    {
        if (!$this->category || $this->category === 'all') {
            return;
        }

        // Если категория - это slug, ищем по slug
        $query->where('category', $this->category);
    }

    /**
     * Фильтр по ценовому диапазону
     */
    private function applyPriceFilter(Builder $query): void
    {
        if (!$this->price_range) {
            return;
        }

        $priceRange = explode('-', $this->price_range);
        $minPrice = $priceRange[0] ?? null;
        $maxPrice = $priceRange[1] ?? null;

        if ($minPrice && is_numeric($minPrice)) {
            $query->where('price', '>=', (float) $minPrice);
        }

        if ($maxPrice && is_numeric($maxPrice)) {
            $query->where('price', '<=', (float) $maxPrice);
        }
    }

    /**
     * Фильтр по минимальному рейтингу
     */
    private function applyRatingFilter(Builder $query): void
    {
        if (!$this->min_rating) {
            return;
        }

        $query->whereHas('reviews', function ($q) {
            $q->selectRaw('AVG(rating) as avg_rating')
              ->havingRaw('AVG(rating) >= ?', [$this->min_rating]);
        }, '>=', $this->min_rating);
    }

    /**
     * Фильтр по тегам (если используется taggable)
     */
    private function applyTagsFilter(Builder $query): void
    {
        if (empty($this->tags)) {
            return;
        }

        $query->withAllTags($this->tags);
    }

    /**
     * Фильтр по наличию скидки
     */
    private function applyDiscountFilter(Builder $query): void
    {
        if (!$this->with_discount) {
            return;
        }

        $query->where(function ($q) {
            $q->where('discounted_price', '>', 0)
              ->orWhere('has_promotion', true);
        });
    }

    /**
     * Применяет сортировку в зависимости от параметра
     */
    private function applySorting(Builder $query): void
    {
        switch ($this->sort_by) {
            case 'price_asc':
                $query->orderBy('price');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name_asc':
                $query->orderBy('name');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'rating':
                $query->withAvg('reviews', 'rating');

            case 'popularity':
                $query->withCount('bookings')
                      ->orderBy('bookings_count', 'desc');
                break;

            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'featured':
                $query->where('is_featured', true)
                      ->orderBy('featured_order');
                break;

            default:
                $query->orderBy('order_column')
                      ->orderBy('created_at', 'desc');
                break;
        }
    }

    /**
     * Добавляет eager loading отношений для оптимизации запросов
     */
    private function withRelations(Builder $query): void
    {
        $query->with([
            'media' => function ($q) {
                $q->where('collection_name', 'service_images')
                  ->orderBy('order_column');
            },
            'reviews' => function ($q) {
                $q->where('approved', true)
                  ->orderBy('created_at', 'desc')
                  ->limit(3);
            }
        ]);
    }

    /**
     * Генерирует уникальный ключ кеша для текущих фильтров
     */
    public function cacheKey(string $prefix = 'services_filter'): string
    {
        $params = [
            'category' => $this->category,
            'price_range' => $this->price_range,
            'sort_by' => $this->sort_by,
            'search' => $this->search,
            'per_page' => $this->per_page,
            'only_active' => $this->only_active,
            'tags' => $this->tags,
            'min_rating' => $this->min_rating,
            'with_discount' => $this->with_discount
        ];

        return $prefix . ':' . md5(serialize($params));
    }

    /**
     * Получает отфильтрованные услуги с кешированием в Redis
     */
    public function getCachedResults(): LengthAwarePaginator
    {
        $cacheKey = $this->cacheKey();
        $cacheTags = ['services', 'services_filtered'];

        // Пытаемся получить из кеша
        if ($cached = Redis::get($cacheKey)) {
            return unserialize($cached);
        }

        // Если нет в кеше, выполняем запрос
        $query = Service::query();
        $this->apply($query);

        $results = $query->paginate($this->per_page);

        // Кешируем результаты на 1 час
        Redis::setex($cacheKey, 3600, serialize($results));

        // Добавляем теги для инвалидации
        Redis::sadd('service_filter_tags:' . md5($cacheKey), $cacheTags);

        return $results;
    }

    /**
     * Инвалидирует кеш для конкретного фильтра
     */
    public function invalidateCache(): void
    {
        $cacheKey = $this->cacheKey();
        Redis::del($cacheKey);

        // Удаляем связанные теги
        $tagKey = 'service_filter_tags:' . md5($cacheKey);
        $tags = Redis::smembers($tagKey);

        foreach ($tags as $tag) {
            Redis::srem($tagKey, $tag);
        }
    }

    /**
     * Инвалидирует весь кеш услуг
     */
    public static function invalidateAllCache(): void
    {
        // Находим все ключи, связанные с фильтрами услуг
        $keys = Redis::keys('services_filter:*');

        foreach ($keys as $key) {
            Redis::del($key);
        }

        // Очищаем теги
        $tagKeys = Redis::keys('service_filter_tags:*');
        foreach ($tagKeys as $tagKey) {
            Redis::del($tagKey);
        }
    }

    /**
     * Получает доступные опции фильтрации для UI
     */
    public static function getFilterOptions(): array
    {
        $cacheKey = 'service_filter_options';

        // Пытаемся получить из кеша
        if ($cached = Redis::get($cacheKey)) {
            return unserialize($cached);
        }

        $options = [
            'price_ranges' => self::getPriceRanges(),
            'sort_options' => self::getSortOptions(),
            'categories' => self::getCategoryOptions(),
            'tags' => self::getTagOptions(),
        ];

        // Кешируем на 24 часа
        Redis::setex($cacheKey, 86400, serialize($options));

        return $options;
    }

    /**
     * Доступные ценовые диапазоны
     */
    private static function getPriceRanges(): array
    {
        return [
            ['label' => 'Любая цена', 'value' => null],
            ['label' => 'До 3 000₽', 'value' => '0-3000'],
            ['label' => '3 000 - 5 000₽', 'value' => '3000-5000'],
            ['label' => '5 000 - 8 000₽', 'value' => '5000-8000'],
            ['label' => '8 000 - 12 000₽', 'value' => '8000-12000'],
            ['label' => 'От 12 000₽', 'value' => '12000-15000'],
        ];
    }

    /**
     * Опции сортировки
     */
    private static function getSortOptions(): array
    {
        return [
            ['label' => 'По популярности', 'value' => 'popularity'],
            ['label' => 'По рейтингу', 'value' => 'rating'],
            ['label' => 'По цене: от дешевых', 'value' => 'price_asc'],
            ['label' => 'По цене: от дорогих', 'value' => 'price_desc'],
            ['label' => 'По названию (А-Я)', 'value' => 'name_asc'],
            ['label' => 'По названию (Я-А)', 'value' => 'name_desc'],
            ['label' => 'Новинки', 'value' => 'newest'],
            ['label' => 'Рекомендуемые', 'value' => 'featured'],
        ];
    }

    /**
     * Опции категорий
     */
    private static function getCategoryOptions(): array
    {
        return Cache::remember('service_categories_options', 3600, function () {
            return [];
        });
    }

    /**
     * Опции тегов
     */
    private static function getTagOptions(): array
    {
        if (!class_exists(\Spatie\Tags\Tag::class)) {
            return [];
        }

        return Cache::remember('service_tags_options', 3600, function () {
            return \Spatie\Tags\Tag::query()
                ->where('type', 'service')
                ->withCount(['services' => function ($q) {
                    $q->where('status', ServiceStatus::ACTIVE);
                }])
                ->orderBy('services_count', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'count' => $tag->services_count,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Статистика по фильтрам для аналитики
     */
    public function getStats(): array
    {
        return [
            'active_filters' => array_filter([
                'category' => $this->category,
                'price_range' => $this->price_range,
                'sort_by' => $this->sort_by,
                'search' => $this->search ? 'yes' : 'no',
                'with_discount' => $this->with_discount,
            ]),
            'filter_hash' => $this->cacheKey(),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

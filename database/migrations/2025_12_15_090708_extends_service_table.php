<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Slug для ЧПУ
            $table->string('slug')
                ->nullable()
                ->unique()
                ->after('name')
                ->comment('URL-friendly identifier');

            // Короткое описание для карточек и превью
            $table->text('short_description')
                ->nullable()
                ->after('description')
                ->comment('Short description for cards and previews');

            // Детальное описание для страницы услуги
            $table->text('detailed_description')
                ->nullable()
                ->after('short_description')
                ->comment('Detailed description with HTML formatting');

            // Цена со скидкой
            $table->decimal('discounted_price', 8, 2)
                ->nullable()
                ->after('price')
                ->comment('Discounted price for promotions');

            // Продолжительность процедуры
            $table->string('duration')
                ->nullable()
                ->after('discounted_price')
                ->comment('Duration of the procedure (e.g., "2-3 hours")');

            // Порядок сортировки
            $table->integer('sort_order')
                ->default(0)
                ->after('duration')
                ->comment('Sorting order within category');

            // Рейтинг
            $table->decimal('rating', 3, 2)
                ->default(0)
                ->after('sort_order')
                ->comment('Average rating from reviews');

            // Количество отзывов
            $table->integer('reviews_count')
                ->default(0)
                ->after('rating')
                ->comment('Total number of reviews');

            // Количество просмотров
            $table->integer('views_count')
                ->default(0)
                ->after('reviews_count')
                ->comment('Number of page views');

            // Флаг активности
            $table->boolean('is_active')
                ->default(true)
                ->after('views_count')
                ->comment('Whether service is active and visible');

            // Флаг популярности
            $table->boolean('is_popular')
                ->default(false)
                ->after('is_active')
                ->comment('Marked as popular by admin');

            // Флаг наличия акции
            $table->boolean('has_promotion')
                ->default(false)
                ->after('is_popular')
                ->comment('Whether service has active promotion');

            // SEO поля
            $table->string('meta_title')
                ->nullable()
                ->after('has_promotion')
                ->comment('Custom meta title for SEO');

            $table->text('meta_description')
                ->nullable()
                ->after('meta_title')
                ->comment('Custom meta description for SEO');

            $table->text('meta_keywords')
                ->nullable()
                ->after('meta_description')
                ->comment('Custom meta keywords for SEO');

            // Преимущества (JSON массив)
            $table->json('benefits')
                ->nullable()
                ->after('meta_keywords')
                ->comment('Array of benefits for this service');

            // Противопоказания (JSON массив)
            $table->json('contraindications')
                ->nullable()
                ->after('benefits')
                ->comment('Array of contraindications');

            // Даты для SEO и архивирования
            $table->timestamp('published_at')
                ->nullable()
                ->after('contraindications')
                ->comment('Publication date for SEO');

            $table->timestamp('featured_until')
                ->nullable()
                ->after('published_at')
                ->comment('Until when service is featured');
        });

        // Создаем индексы для оптимизации запросов
        Schema::table('services', function (Blueprint $table) {
            // Составной индекс для фильтрации по категории и активности
            $table->index(['category', 'is_active', 'sort_order']);

            // Индекс для сортировки по популярности
            $table->index(['is_popular', 'views_count']);

            // Индекс для поиска по цене
            $table->index(['price', 'discounted_price']);

            // Индекс для сортировки по рейтингу
            $table->index(['rating', 'reviews_count']);

            // Full-text индекс для поиска
            $table->fullText(['name', 'description', 'short_description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Удаляем индексы в обратном порядке
            $table->dropIndex(['category', 'is_active', 'sort_order']);
            $table->dropIndex(['is_popular', 'views_count']);
            $table->dropIndex(['price', 'discounted_price']);
            $table->dropIndex(['rating', 'reviews_count']);

            // Удаляем full-text индекс
            $table->dropFullText(['name', 'description', 'short_description']);

            // Удаляем поля в обратном порядке добавления
            $table->dropColumn([
                'featured_until',
                'published_at',
                'contraindications',
                'benefits',
                'meta_keywords',
                'meta_description',
                'meta_title',
                'has_promotion',
                'is_popular',
                'is_active',
                'views_count',
                'reviews_count',
                'rating',
                'sort_order',
                'duration',
                'discounted_price',
                'detailed_description',
                'short_description',
                'slug',
            ]);
        });
    }
};

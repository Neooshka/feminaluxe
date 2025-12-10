<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Sitemap\SitemapGenerator;
use App\Models\Post;
use App\Models\Category;
use App\Models\Page;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * Регистрация сервисов.
     */
    public function register(): void
    {
        $this->app->singleton('sitemap.generator', function ($app) {
            return new class() {
                public function generate()
                {
                    // Генерируем sitemap
                    SitemapGenerator::create(config('app.url'))
                        ->getSitemap()
                        // Добавляем статические страницы
                        ->add(url('/'))
                        ->add(url('/about'))
                        ->add(url('/contact'))
                        ->add(url('/blog'))
                        ->add(url('/portfolio'))

                        // Добавляем категории
                        ->add($this->getCategories())

                        // Добавляем посты
                        ->add($this->getPosts())

                        // Добавляем страницы
                        ->add($this->getPages())

                        // Записываем в файл
                        ->writeToFile(public_path('sitemap.xml'));

                    return true;
                }

                /**
                 * Получаем категории для sitemap
                 */
                private function getCategories()
                {
                    return Category::where('is_active', true)
                        ->whereNotNull('slug')
                        ->get()
                        ->map(function ($category) {
                            return url("/category/{$category->slug}");
                        })
                        ->toArray();
                }

                /**
                 * Получаем посты для sitemap
                 */
                private function getPosts()
                {
                    return Post::where('published', true)
                        ->where('published_at', '<=', now())
                        ->whereNotNull('slug')
                        ->get()
                        ->map(function ($post) {
                            return [
                                'url' => url("/blog/{$post->slug}"),
                                'lastmod' => $post->updated_at->format('Y-m-d'),
                                'changefreq' => 'weekly',
                                'priority' => 0.8,
                            ];
                        })
                        ->toArray();
                }

                /**
                 * Получаем статические страницы для sitemap
                 */
                private function getPages()
                {
                    return Page::where('is_active', true)
                        ->whereNotNull('slug')
                        ->get()
                        ->map(function ($page) {
                            return [
                                'url' => url("/page/{$page->slug}"),
                                'lastmod' => $page->updated_at->format('Y-m-d'),
                                'changefreq' => 'monthly',
                                'priority' => 0.6,
                            ];
                        })
                        ->toArray();
                }
            };
        });
    }

    /**
     * Загрузка сервисов.
     */
    public function boot(): void
    {
        // Можно добавить дополнительные настройки здесь
    }
}

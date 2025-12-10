<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Tag;

class GenerateSitemap extends Command
{
    /**
     * Название и сигнатура команды.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Генерация sitemap.xml';

    /**
     * Выполнение команды.
     */
    public function handle()
    {
        $this->info('Начинаем генерацию sitemap...');

        // Создаем экземпляр sitemap
        $sitemap = Sitemap::create();

        // 1. Добавляем главную страницу
        $sitemap->add(
            Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        );

        // 2. Добавляем статические страницы
        $this->addStaticPages($sitemap);

        // 3. Добавляем категории (если есть)
        if (class_exists(Category::class)) {
            $this->addCategories($sitemap);
        }

        // 4. Добавляем посты/статьи (если есть)
        if (class_exists(Post::class)) {
            $this->addPosts($sitemap);
        }

        // 5. Добавляем продукты (если есть)
        if (class_exists(Product::class)) {
            $this->addProducts($sitemap);
        }

        // 6. Добавляем теги (если есть)
        if (class_exists(Tag::class)) {
            $this->addTags($sitemap);
        }

        // 7. Добавляем страницы (если есть)
        if (class_exists(Page::class)) {
            $this->addCustomPages($sitemap);
        }

        // 8. Добавляем дополнительные URL
        $this->addAdditionalUrls($sitemap);

        // Сохраняем sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        // Создаем robots.txt (опционально)
        $this->createRobotsTxt();

        $this->info('Sitemap успешно сгенерирован!');
        $this->info('Файл доступен по адресу: ' . url('sitemap.xml'));

        return Command::SUCCESS;
    }

    /**
     * Добавление статических страниц.
     */
    private function addStaticPages(Sitemap $sitemap): void
    {
        $staticPages = [
            ['url' => '/about', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['url' => '/contact', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_YEARLY],
            ['url' => '/blog', 'priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['url' => '/portfolio', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['url' => '/services', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['url' => '/pricing', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['url' => '/faq', 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_YEARLY],
            ['url' => '/privacy-policy', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_YEARLY],
            ['url' => '/terms-of-service', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_YEARLY],
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['frequency'])
            );
        }
    }

    /**
     * Добавление категорий.
     */
    private function addCategories(Sitemap $sitemap): void
    {
        try {
            $categories = Category::where('is_active', true)
                ->whereNotNull('slug')
                ->get();

            foreach ($categories as $category) {
                $sitemap->add(
                    Url::create("/category/{$category->slug}")
                        ->setPriority(0.7)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setLastModificationDate($category->updated_at)
                );
            }

            $this->info("Добавлено категорий: " . $categories->count());
        } catch (\Exception $e) {
            $this->warn("Не удалось добавить категории: " . $e->getMessage());
        }
    }

    /**
     * Добавление постов.
     */
    private function addPosts(Sitemap $sitemap): void
    {
        try {
            $posts = Post::where('published', true)
                ->where('published_at', '<=', now())
                ->whereNotNull('slug')
                ->get();

            foreach ($posts as $post) {
                $sitemap->add(
                    Url::create("/blog/{$post->slug}")
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setLastModificationDate($post->updated_at)
                );
            }

            $this->info("Добавлено постов: " . $posts->count());
        } catch (\Exception $e) {
            $this->warn("Не удалось добавить посты: " . $e->getMessage());
        }
    }

    /**
     * Добавление продуктов.
     */
    private function addProducts(Sitemap $sitemap): void
    {
        try {
            $products = Product::where('is_active', true)
                ->whereNotNull('slug')
                ->get();

            foreach ($products as $product) {
                $sitemap->add(
                    Url::create("/product/{$product->slug}")
                        ->setPriority(0.9)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setLastModificationDate($product->updated_at)
                );
            }

            $this->info("Добавлено продуктов: " . $products->count());
        } catch (\Exception $e) {
            $this->warn("Не удалось добавить продукты: " . $e->getMessage());
        }
    }

    /**
     * Добавление тегов.
     */
    private function addTags(Sitemap $sitemap): void
    {
        try {
            $tags = Tag::whereHas('posts', function ($query) {
                    $query->where('published', true);
                })
                ->whereNotNull('slug')
                ->get();

            foreach ($tags as $tag) {
                $sitemap->add(
                    Url::create("/tag/{$tag->slug}")
                        ->setPriority(0.5)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setLastModificationDate($tag->updated_at)
                );
            }

            $this->info("Добавлено тегов: " . $tags->count());
        } catch (\Exception $e) {
            $this->warn("Не удалось добавить теги: " . $e->getMessage());
        }
    }

    /**
     * Добавление кастомных страниц.
     */
    private function addCustomPages(Sitemap $sitemap): void
    {
        try {
            $pages = Page::where('is_active', true)
                ->whereNotNull('slug')
                ->get();

            foreach ($pages as $page) {
                $sitemap->add(
                    Url::create("/page/{$page->slug}")
                        ->setPriority(0.6)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setLastModificationDate($page->updated_at)
                );
            }

            $this->info("Добавлено страниц: " . $pages->count());
        } catch (\Exception $e) {
            $this->warn("Не удалось добавить страницы: " . $e->getMessage());
        }
    }

    /**
     * Добавление дополнительных URL.
     */
    private function addAdditionalUrls(Sitemap $sitemap): void
    {
        $additionalUrls = config('sitemap.additional_urls', []);

        foreach ($additionalUrls as $url) {
            $sitemap->add(
                Url::create($url['path'])
                    ->setPriority($url['priority'] ?? 0.5)
                    ->setChangeFrequency($url['frequency'] ?? Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }
    }

    /**
     * Создание/обновление robots.txt.
     */
    private function createRobotsTxt(): void
    {
        $robotsContent = "User-agent: *\n";
        $robotsContent .= "Allow: /\n";
        $robotsContent .= "Disallow: /admin/\n";
        $robotsContent .= "Disallow: /auth/\n";
        $robotsContent .= "Disallow: /api/\n";
        $robotsContent .= "Sitemap: " . url('sitemap.xml') . "\n";

        file_put_contents(public_path('robots.txt'), $robotsContent);

        $this->info('Robots.txt обновлен');
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\Blog\Http\Controllers;

use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Models\Tag;
use App\Domains\Blog\Services\BlogSeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController
{
    public function __construct(
        private BlogSeoService $seoService
    ) {}

    public function index(Request $request): Response
    {
        $query = Post::published()
            ->with(['tags', 'media'])
            ->latest('published_at');

        // Фильтр по тегам
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $posts = $query->paginate(9)
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => Str::limit(strip_tags($post->content), 150),
                    'published_at' => $post->published_at->format('d.m.Y'),
                    'published_at_full' => $post->published_at->isoFormat('D MMMM YYYY'),
                    'reading_time' => $this->calculateReadingTime($post->content),
                    'featured_image' => $post->getFirstMediaUrl('featured', 'thumb') ?: 
                        asset('img/blog/fallback-thumb.svg'),
                    'featured_image_large' => $post->getFirstMediaUrl('featured') ?: 
                        asset('img/blog/fallback-post.svg'),
                    'tags' => $post->tags->map(fn($tag) => [
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ]),
                ];
            });

        // Популярные теги
        $popularTags = Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($tag) => [
                'name' => $tag->name,
                'slug' => $tag->slug,
                'count' => $tag->posts_count,
            ]);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'popularTags' => $popularTags,
            'filters' => $request->only(['tag']),
        ])->withViewData([
            'SEOData' => $this->seoService->generateIndexSeoData()
        ]);
    }

    public function show(Post $post): Response
    {
        // Проверка публикации
        if (!$post->published_at || $post->published_at > now()) {
            abort(404);
        }

        // Увеличиваем счетчик просмотров (если нужно)
         $post->increment('views_count');

        // Похожие статьи
        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('tags', function ($query) use ($post) {
                $query->whereIn('tags.id', $post->tags->pluck('id'));
            })
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'excerpt' => Str::limit(strip_tags($item->content), 100),
                    'featured_image' => $item->getFirstMediaUrl('featured', 'thumb'),
                    'published_at' => $item->published_at->format('d.m.Y'),
                ];
            });

        // Все теги статьи
        $tags = $post->tags->map(fn($tag) => [
            'name' => $tag->name,
            'slug' => $tag->slug,
        ]);

        return Inertia::render('Blog/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'published_at' => $post->published_at->format('d.m.Y'),
                'published_at_full' => $post->published_at->isoFormat('D MMMM YYYY'),
                'reading_time' => $this->calculateReadingTime($post->content),
                'featured_image' => $post->getFirstMediaUrl('featured') ?: 
                    asset('img/blog/fallback-post.svg'),
                'tags' => $tags,
                'author' => [
                    'name' => 'Мастер Feminaluxe',
                    'avatar' => asset('img/team/default-avatar.svg'),
                    'bio' => 'Профессиональный мастер перманентного макияжа с 5-летним опытом',
                ],
            ],
            'related' => $related,
        ])->withViewData([
            'SEOData' => $post->getDynamicSEOData(),
        ]);
    }

    private function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200); // 200 слов в минуту
        return max(1, $minutes);
    }
}
<?php

declare(strict_types=1);

namespace App\Domains\Blog\Http\Controllers;

use App\Domains\Blog\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()->with('media')->latest('published_at')->paginate(10);

        return Inertia::render('Blog/Index', [
            'posts' => $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => \Illuminate\Support\Str::limit(strip_tags($post->content), 150),
                    'published_at' => $post->published_at->format('d.m.Y'),
                    'featured_image' => $post->getFirstMediaUrl('featured', 'thumb'),
                ];
            }),
            'pagination' => $posts->links(),
        ])->withViewData([
            'seo' => [
                'title' => 'Блог Feminaluxe: Советы по красоте и макияжу',
                'description' => 'Полезные статьи о перманентном макияже, уходе за кожей и трендах 2025.',
            ],
        ]);
    }

    public function show(Post $post): Response
    {
        if (!$post->published_at || $post->published_at > now()) {
            abort(404);
        }

        return Inertia::render('Blog/Show', [
            'post' => [
                'title' => $post->title,
                'content' => $post->content,  // Assume markdown parsed in Vue
                'published_at' => $post->published_at->format('d.m.Y'),
                'featured_image' => $post->getFirstMediaUrl('featured'),
            ],
        ])->withViewData([
            'seo' => [
                'title' => $post->title . ' | Feminaluxe Блог',
                'description' => \Illuminate\Support\Str::limit(strip_tags($post->content), 150),
            ],
        ]);
    }
}

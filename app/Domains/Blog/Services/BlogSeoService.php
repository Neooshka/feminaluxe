<?php

declare(strict_types=1);

namespace App\Domains\Blog\Services;

use Carbon\Carbon;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class BlogSeoService
{
    public function generateIndexSeoData(): SEOData
    {
        return new SEOData(
            title: 'Блог о перманентном макияже | Советы, тренды, уход | Feminaluxe',
            description: 'Полезные статьи о перманентном макияже, уходе за татуажем, трендах 2025 года. Советы от мастеров студии Feminaluxe.',
            author: 'Feminaluxe Studio',
            image: asset('img/blog/og-index.jpg'),
            published_time: Carbon::now(),
            modified_time: Carbon::now(),
            robots: 'index, follow',
            canonical_url: route('blog.index'),
            openGraphTitle: 'Блог о красоте и перманентном макияже | Feminaluxe'
        );
    }

    public function generateCategorySeoData(string $category): SEOData
    {
        $titles = [
            'brows' => 'Брови: коррекция, уход, тренды | Блог Feminaluxe',
            'lips' => 'Губы: перманентный макияж, уход | Блог Feminaluxe',
            'care' => 'Уход после процедуры | Советы от мастеров',
            'trends' => 'Тренды перманентного макияжа 2025',
        ];

        return new SEOData(
            title: $titles[$category] ?? 'Статьи о красоте | Блог Feminaluxe',
            description: 'Полезные статьи и советы по теме ' . $category,
            canonical_url: route('blog.index', ['category' => $category]),
        );
    }
}
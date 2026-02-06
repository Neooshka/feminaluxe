<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Blog\Models\Post;
use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Services\Models\Service;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap.xml file';

    public function handle(): int
    {
        $services = Service::active()->get('slug')->map(function ($service) {
            return '/services/' . $service->slug;
        });
        
        $portfolio = Portfolio::published()->get('slug')->map(function ($portfolio) {
            return '/portfolio/' . $portfolio->slug;
        });
        
        $blog = Post::published()->get('slug')->map(function ($post) {
            return '/blog/' . $post->slug;
        });
        
        $sitemap = Sitemap::create()
            ->add(url('/'))
            ->add(url('/services'))
            ->add(url('/portfolio'))
            ->add(url('/reviews'))
            ->add(url('/blog'))
            ->add(url('/contact'))
            ->add(url('/bookings/create'))
            ->add(url('/privacy'))
            ->add(url('/agreement'))
            ->add($services)
            ->add($portfolio)
            ->add($blog);

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');

        return 0;
    }
}

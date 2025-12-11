<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Blog\Models\Post;
use App\Domains\Services\Models\Service;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap.xml file';

    public function handle(): int
    {
        $sitemap = Sitemap::create()
            ->add(Service::all())
            ->add(Post::published()->get())
            ->add(url('/'))
            ->add(url('/services'))
            ->add(url('/portfolio'))
            ->add(url('/about'))
            ->add(url('/contact'))
            ->add(url('/blog'));

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');

        return 0;
    }
}

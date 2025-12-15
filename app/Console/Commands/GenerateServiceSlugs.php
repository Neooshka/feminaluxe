<?php

namespace App\Console\Commands;

use App\Domains\Services\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateServiceSlugs extends Command
{
    protected $signature = 'services:generate-slugs {--force : Force regenerate all slugs}';
    protected $description = 'Generate slugs for services';

    public function handle(): int
    {
        $services = Service::query();

        if (!$this->option('force')) {
            $services->whereNull('slug');
        }

        $services = $services->get();

        $this->info("Generating slugs for {$services->count()} services...");

        $bar = $this->output->createProgressBar($services->count());
        $bar->start();

        foreach ($services as $service) {
            $this->generateUniqueSlug($service);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Slugs generated successfully!');

        return Command::SUCCESS;
    }

    private function generateUniqueSlug(Service $service): void
    {
        $slugBase = Str::slug($service->name);
        $slug = $slugBase;
        $counter = 1;

        while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $service->slug = $slug;
        $service->save();
    }
}

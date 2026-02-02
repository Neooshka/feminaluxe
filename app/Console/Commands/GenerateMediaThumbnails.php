<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GenerateMediaThumbnails extends Command
{
    protected $signature = 'media:generate-thumbnails {--all : All media} {--type= : thumb, small, medium}';
    protected $description = 'Generate thumbnails for media';

    public function handle()
    {
        $query = Media::query();
        
        if (!$this->option('all')) {
            $query->where('model_type', 'App\Domains\Services\Models\Service');
        }
        
        $types = $this->option('type') ? [$this->option('type')] : ['thumb', 'small', 'medium'];
        
        $medias = $query->get();
        
        $this->info("Generating thumbnails for {$medias->count()} media files...");
        
        $bar = $this->output->createProgressBar($medias->count() * count($types));
        
        foreach ($medias as $media) {
            foreach ($types as $type) {
                try {
                    // "Теплим" кэш - делаем запрос для генерации
                    $url = route('media.thumbnail', [
                        'mediaId' => $media->id,
                        'type' => $type
                    ]);
                    
                    file_get_contents($url);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("Error for media {$media->id}: {$e->getMessage()}");
                }
            }
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Thumbnails generated successfully!');
    }
}
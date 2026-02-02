<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaThumbnailController extends Controller
{
    protected $manager;
    
    public function __construct()
    {
        $driver = extension_loaded('imagick')
            ? new ImagickDriver()
            : new GdDriver();
        
        $this->manager = new ImageManager($driver);
    }
    /**
     * Генерация миниатюры на лету
     */
    public function show(Request $request, $mediaId, string $type = 'thumb')
    {
        $cacheKey = "media:{$mediaId}:{$type}";
        
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            return response($cached['content'])
                ->header('Content-Type', $cached['type'])
                ->header('Cache-Control', 'public, max-age=2592000')
                ->header('X-Cache', 'HIT');
        }
        
        $media = Media::findOrFail($mediaId);
        $config = $this->getThumbnailConfig($type);
        $originalPath = $media->getPath();
        
        if (!file_exists($originalPath)) {
            abort(404, 'Original file not found');
        }
        
        $image = $this->manager->read($originalPath);
        
        switch ($config['mode']) {
            case 'cover':
                $image->cover($config['width'], $config['height']);
                break;
            case 'resize':
                $image->resize($config['width'], $config['height']);
                break;
            case 'crop':
                $image->crop($config['width'], $config['height']);
                break;
        }
        
        if ($config['optimize']) {
            $image->sharpen($config['sharpen']);
        }
        
        if ($config['format'] === 'webp' && $this->supportsWebP()) {
            $encoded = $image->toWebp($config['quality']);
            $contentType = 'image/webp';
        } else {
            $encoded = $image->toJpeg($config['quality']);
            $contentType = 'image/jpeg';
        }
        
        $cacheKey = "media:{$mediaId}:{$type}";
        $cacheTime = now()->addDays(30);
        Cache::put($cacheKey, [
            'content' => $encoded->toString(),
            'type' => $contentType
        ], $cacheTime);
        
        return response($encoded->toString(), 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=2592000') // 30 дней
            ->header('Expires', $cacheTime->format('D, d M Y H:i:s \G\M\T'));
    }
    
    /**
     * Конфигурация для разных типов миниатюр
     */
    private function getThumbnailConfig(string $type): array
    {
        $configs = [
            'thumb' => [
                'width' => 150,
                'height' => 150,
                'mode' => 'fit', // fit, resize, crop
                'format' => 'webp', // webp, jpg, png
                'quality' => 80,
                'optimize' => true,
                'sharpen' => 10,
            ],
            'small' => [
                'width' => 300,
                'height' => 300,
                'mode' => 'resize',
                'format' => 'webp',
                'quality' => 85,
                'optimize' => true,
                'sharpen' => 8,
            ],
            'medium' => [
                'width' => 800,
                'height' => 600,
                'mode' => 'resize',
                'format' => 'webp',
                'quality' => 90,
                'optimize' => true,
                'sharpen' => 5,
            ],
            'square' => [
                'width' => 250,
                'height' => 250,
                'mode' => 'fit',
                'format' => 'webp',
                'quality' => 80,
                'optimize' => true,
                'sharpen' => 10,
            ],
        ];
        
        return $configs[$type] ?? $configs['thumb'];
    }
    
    private function supportsWebP()
    {
        if (!function_exists('gd_info')) {
            return false;
        }
        
        $gdInfo = gd_info();
        return isset($gdInfo['WebP Support']) && $gdInfo['WebP Support'];
    }
}
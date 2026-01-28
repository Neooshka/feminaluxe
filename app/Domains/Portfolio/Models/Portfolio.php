<?php

declare(strict_types=1);

namespace App\Domains\Portfolio\Models;

use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Portfolio extends Model implements HasMedia
{
    use HasFactory, HasSEO, InteractsWithMedia;

    protected $fillable = [
        'service_id',
        'title',
        'slug',
        'description',
        'is_published',
        'published_at',
        'views_count',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Services\Models\Service::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before')
            ->singleFile()
            ->useFallbackUrl('/fallback-before.jpg');

        $this->addMediaCollection('after')
            ->singleFile()
            ->useFallbackUrl('/fallback-after.jpg');

        $this->addMediaCollection('gallery')
            ->useFallbackUrl('/fallback-gallery.jpg')
            ->withResponsiveImages();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
    
    protected static function newFactory()
    {
        return PortfolioFactory::new();
    }
}
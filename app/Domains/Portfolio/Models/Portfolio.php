<?php

declare(strict_types=1);

namespace App\Domains\Portfolio\Models;

use App\Domains\Services\Models\Service;
use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
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
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before')
            ->singleFile()
            ->useFallbackUrl('/storage/img/portfolio/fallback-before.svg');

        $this->addMediaCollection('after')
            ->singleFile()
            ->useFallbackUrl('/storage/img/portfolio/fallback-after.svg');

        $this->addMediaCollection('gallery')
            ->useFallbackUrl('/storage/img/portfolio/fallback-gallery.svg')
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

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->seo_title ?: $this->title . ' | Портфолио Feminaluxe',
            description: $this->seo_description ?:
                Str::limit(strip_tags($this->description), 160),
            author: 'Feminaluxe Studio',
            image: $this->getFirstMediaUrl('before') ?:
                asset('img/seo/portfolio-og.jpg'),
            published_time: $this->published_at,
            modified_time: $this->updated_at,
            robots: $this->is_published ? 'index, follow' : 'noindex, nofollow',
            canonical_url: route('portfolio.show', $this->slug),
            openGraphTitle: $this->seo_title ?: $this->title
        );
    }
    
    protected static function newFactory()
    {
        return PortfolioFactory::new();
    }
}
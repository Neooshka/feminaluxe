<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, HasSEO, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->useFallbackUrl(asset('img/blog/fallback-post.svg'));
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
    
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * SEO данные для пакета ralphjsmit/laravel-seo
     */
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->seo_title ?: $this->title . ' | Блог Feminaluxe',
            description: $this->seo_description ?: 
                Str::limit(strip_tags($this->content), 160),
            author: 'Feminaluxe Studio',
            image: $this->getFirstMediaUrl('featured') ?: 
                asset('img/blog/og-default.svg'),
            published_time: $this->published_at,
            modified_time: $this->updated_at,
            robots: $this->published_at && $this->published_at <= now() 
                ? 'index, follow' 
                : 'noindex, nofollow',
            canonical_url: route('blog.show', $this->slug),
            openGraphTitle: $this->seo_title ?: $this->title
        );
    }
    
    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
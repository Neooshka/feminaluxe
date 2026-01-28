<?php

namespace App\Domains\Services\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Domains\Bookings\Models\Booking;
use App\Domains\Reviews\Models\Review;

class Service extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'slug',
        'name',
        'category',
        'description',
        'short_description',
        'detailed_description',
        'price',
        'discounted_price',
        'duration',
        'sort_order',
        'rating',
        'reviews_count',
        'views_count',
        'is_active',
        'is_popular',
        'has_promotion',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'benefits',
        'contraindications',
        'published_at',
        'featured_until',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'has_promotion' => 'boolean',
        'benefits' => 'array',
        'contraindications' => 'array',
        'published_at' => 'datetime',
        'featured_until' => 'datetime',
        'views_count' => 'integer',
        'reviews_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $dates = [
        'published_at',
        'featured_until',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before_after')
            ->useFallbackUrl('/fallback.jpg');

        $this->addMediaCollection('gallery')
            ->useFallbackUrl('/fallback.jpg')
            ->withResponsiveImages();
    }

    /**
     * Scope for services in a specific category
     */
    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for active services only
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular services
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for services with promotions
     */
    public function scopeWithPromotion(Builder $query): Builder
    {
        return $query->where('has_promotion', true)
            ->whereNotNull('discounted_price')
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '>', now());
            });
    }

    /**
     * Scope for featured services (active promotions)
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->withPromotion()
            ->where('featured_until', '>', now());
    }

    /**
     * Scope for published services
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for services with discount
     */
    public function scopeWithDiscount(Builder $query): Builder
    {
        return $query->whereNotNull('discounted_price')
            ->whereColumn('discounted_price', '<', 'price');
    }

    /**
     * Get final price (discounted or regular)
     */
    public function getFinalPriceAttribute(): float
    {
        if ($this->has_promotion && $this->discounted_price) {
            return (float) $this->discounted_price;
        }

        return (float) $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->discounted_price || $this->discounted_price >= $this->price) {
            return null;
        }

        $discount = (($this->price - $this->discounted_price) / $this->price) * 100;
        return (int) round($discount);
    }

    /**
     * Check if service is on sale
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->has_promotion
            && $this->discounted_price
            && $this->discounted_price < $this->price
            && (!$this->featured_until || $this->featured_until > now());
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('before_after');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get thumbnail image URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('before_after');
        return $media ? $media->getUrl('thumb') : null;
    }

    /**
     * Get all gallery images
     */
    public function getGalleryImagesAttribute(): array
    {
        return $this->getMedia('gallery')->map(function ($media) {
            return [
                'original' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'medium' => $media->getUrl('medium'),
                'alt' => $this->name,
            ];
        })->toArray();
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Update rating based on reviews
     */
    public function updateRating(): void
    {
        $this->rating = $this->reviews()->avg('rating') ?? 0;
        $this->reviews_count = $this->reviews()->count();
        $this->save();
    }

    /**
     * Get related services (same category, exclude current)
     */
    public function relatedServices(int $limit = 3): Builder
    {
        return self::query()
            ->where('category', $this->category)
            ->where('id', '!=', $this->id)
            ->active()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->limit($limit);
    }

    /**
     * Get the route key for the model (for SEO-friendly URLs)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Find service by slug or id
     */
    public static function findBySlugOrId(string|int $identifier): ?self
    {
        if (is_numeric($identifier)) {
            return self::find($identifier);
        }

        return self::where('slug', $identifier)->first();
    }
    
    protected static function newFactory()
    {
        return ServiceFactory::new();
    }
}

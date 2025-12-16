<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Services;

use App\Domains\Reviews\Models\Review;
use App\Domains\Services\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ReviewService
{
    /**
     * Get filtered reviews with caching
     */
    public function getFilteredReviews(array $filters = [])
    {
        $cacheKey = $this->generateCacheKey($filters);

        return Cache::remember($cacheKey, 3600, function () use ($filters) {
            $query = Review::query()
                ->with(['service' => function ($query) {
                    $query->select('id', 'name', 'slug', 'primary_image_url');
                }])
                ->approved()
                ->latest();

            // Filter by service
            if (!empty($filters['service_id'])) {
                $query->where('service_id', $filters['service_id']);
            }

            // Filter by rating
            if (!empty($filters['rating'])) {
                $query->where('rating', $filters['rating']);
            }

            // Search in content
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('content', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('author', 'like', '%' . $filters['search'] . '%');
                });
            }

            // Apply sorting
            switch ($filters['sort'] ?? 'newest') {
                case 'rating_desc':
                    $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                    break;
                case 'rating_asc':
                    $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            return $query->paginate(12);
        });
    }

    /**
     * Get overall review statistics
     */
    public function getReviewStats(): array
    {
        return Cache::remember('reviews_stats', 1800, function () {
            $total = Review::approved()->count();
            $average = round(Review::approved()->avg('rating') ?? 0, 1);

            $ratingDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $count = Review::approved()->where('rating', $i)->count();
                $ratingDistribution[$i] = [
                    'count' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
                ];
            }

            return [
                'total_reviews' => $total,
                'average_rating' => $average,
                'rating_distribution' => $ratingDistribution,
                'services_with_reviews' => Service::whereHas('reviews')->count(),
                'latest_review_date' => Review::approved()->latest()->first()?->created_at?->format('d.m.Y'),
            ];
        });
    }

    /**
     * Get service-specific review statistics
     */
    public function getServiceReviewStats(int $serviceId): array
    {
        return Cache::remember("service_reviews_stats_{$serviceId}", 1800, function () use ($serviceId) {
            $reviews = Review::where('service_id', $serviceId)->approved();
            $total = $reviews->count();
            $average = round($reviews->avg('rating') ?? 0, 1);

            $ratingDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $count = $reviews->where('rating', $i)->count();
                $ratingDistribution[$i] = [
                    'count' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
                ];
            }

            return [
                'total_reviews' => $total,
                'average_rating' => $average,
                'rating_distribution' => $ratingDistribution,
            ];
        });
    }

    /**
     * Get latest reviews for homepage/widget
     */
    public function getLatestReviews(int $limit = 6): Collection
    {
        return Cache::remember("latest_reviews_{$limit}", 1800, function () use ($limit) {
            return Review::with(['service:id,name,slug'])
                ->approved()
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'author' => $review->author,
                        'content' => $this->truncateContent($review->content, 120),
                        'rating' => $review->rating,
                        'service_name' => $review->service->name,
                        'service_slug' => $review->service->slug,
                        'created_at' => $review->created_at->format('d.m.Y'),
                        'avatar_color' => $this->generateAvatarColor($review->author),
                    ];
                });
        });
    }

    /**
     * Generate cache key based on filters
     */
    private function generateCacheKey(array $filters): string
    {
        ksort($filters);
        return 'reviews_filtered_' . md5(serialize($filters));
    }

    /**
     * Truncate content without breaking words
     */
    private function truncateContent(string $content, int $length): string
    {
        if (mb_strlen($content) <= $length) {
            return $content;
        }

        $truncated = mb_substr($content, 0, $length);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }

    /**
     * Generate avatar color
     */
    private function generateAvatarColor(string $author): string
    {
        $colors = [
            'from-pink-500 to-rose-500',
            'from-purple-500 to-indigo-500',
            'from-blue-500 to-cyan-500',
            'from-green-500 to-emerald-500',
            'from-yellow-500 to-amber-500',
            'from-orange-500 to-red-500',
        ];

        $hash = crc32($author);
        return $colors[$hash % count($colors)];
    }
}

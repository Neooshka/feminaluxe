<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Http\Controllers;

use App\Domains\Reviews\Models\Review;
use App\Domains\Reviews\Services\ReviewService;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewService $reviewService
    ) {}

    /**
     * Display reviews index page
     */
    public function index(Request $request): Response
    {
        // Получаем параметры фильтрации
        $filters = [
            'service_id' => $request->input('service'),
            'rating' => $request->input('rating'),
            'sort' => $request->input('sort', 'newest'),
            'search' => $request->input('search'),
        ];

        // Получаем отзывы с фильтрами
        $reviews = $this->reviewService->getFilteredReviews($filters);

        // Получаем статистику
        $stats = $this->reviewService->getReviewStats();

        // Получаем список услуг для фильтра
        $services = Service::query()
            ->whereHas('reviews')
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'reviews_count' => $service->reviews()->approved()->count(),
            ]);

        // Форматируем отзывы для отображения
        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'author' => $review->author,
                'content' => $review->content,
                'rating' => $review->rating,
                'service_name' => $review->service->name ?? 'Услуга',
                'service_slug' => $review->service->slug ?? '#',
                'created_at' => $review->created_at->format('d.m.Y'),
                'created_at_human' => $review->created_at->diffForHumans(),
                'avatar_color' => $this->generateAvatarColor($review->author),
            ];
        });

        return Inertia::render('Reviews/Index', [
            'reviews' => $formattedReviews,
            'services' => $services,
            'stats' => $stats,
            'filters' => $filters,
            'meta' => [
                'title' => 'Отзывы о перманентном макияже | Feminaluxe Балашиха',
                'description' => 'Реальные отзывы клиентов о перманентном макияже бровей, губ и межресничного пространства. Фото до и после, оценки, рекомендации.',
                'keywords' => 'отзывы перманентный макияж, отзывы татуаж бровей, отзывы татуаж губ, отзывы межресничный перманент',
            ]
        ]);
    }

    /**
     * Display service-specific reviews
     */
    public function serviceReviews(Request $request, string $serviceSlug): Response
    {
        $service = Service::where('slug', $serviceSlug)->active()->firstOrFail();

        $filters = [
            'service_id' => $service->id,
            'rating' => $request->input('rating'),
            'sort' => $request->input('sort', 'newest'),
        ];

        $reviews = $this->reviewService->getFilteredReviews($filters);

        $serviceStats = $this->reviewService->getServiceReviewStats($service->id);

        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'author' => $review->author,
                'content' => $review->content,
                'rating' => $review->rating,
                'created_at' => $review->created_at->format('d.m.Y'),
                'created_at_human' => $review->created_at->diffForHumans(),
                'avatar_color' => $this->generateAvatarColor($review->author),
            ];
        });

        return Inertia::render('Reviews/Service', [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->short_description,
                'price' => $service->final_price,
                'image' => $service->primary_image_url,
            ],
            'reviews' => $formattedReviews,
            'stats' => $serviceStats,
            'filters' => $filters,
            'meta' => [
                'title' => "Отзывы об услуге {$service->name} | Feminaluxe",
                'description' => "Реальные отзывы клиентов об услуге {$service->name}. Рейтинг {$serviceStats['average_rating']} из 5 на основе {$serviceStats['total_reviews']} отзывов.",
            ]
        ]);
    }

    /**
     * API endpoint for submitting new review
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'author' => ['required', 'string', 'min:2', 'max:100'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'consent' => ['required', 'accepted'],
        ]);

        // Создаем отзыв (по умолчанию не подтвержден)
        $review = Review::create([
            'service_id' => $validated['service_id'],
            'author' => $validated['author'],
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'approved' => false, // Требует модерации
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Отправляем уведомление администратору (можно добавить позже)
        // Notification::send($admins, new NewReviewSubmitted($review));

        return response()->json([
            'success' => true,
            'message' => 'Спасибо за ваш отзыв! Он будет опубликован после проверки модератором.',
            'review_id' => $review->id,
        ]);
    }

    /**
     * API endpoint for getting reviews for service (AJAX)
     */
    public function getServiceReviews(Request $request, Service $service): JsonResponse
    {
        $reviews = $service->reviews()
            ->approved()
            ->orderBy('created_at', 'desc')
            ->limit($request->input('limit', 5))
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'author' => $review->author,
                    'content' => $review->content,
                    'rating' => $review->rating,
                    'created_at' => $review->created_at->format('d.m.Y'),
                    'avatar_color' => $this->generateAvatarColor($review->author),
                ];
            });

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => round($service->reviews()->approved()->avg('rating') ?? 0, 1),
            'total_reviews' => $service->reviews()->approved()->count(),
        ]);
    }

    /**
     * Generate consistent avatar color based on author name
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
            'from-teal-500 to-green-500',
            'from-indigo-500 to-blue-500',
        ];

        $hash = crc32($author);
        return $colors[$hash % count($colors)];
    }
}

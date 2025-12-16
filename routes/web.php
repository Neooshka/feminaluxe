<?php

declare(strict_types=1);

use App\Domains\Blog\Http\Controllers\PostController;
use App\Domains\Bookings\Http\Controllers\BookingController;
use App\Domains\Reviews\Http\Controllers\ReviewController;
use App\Domains\Services\Http\Controllers\ServiceController;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Tighten\Ziggy\Ziggy;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('services')->middleware(['throttle:60,1'])->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/filter', [ServiceController::class, 'filter'])->name('filter');
    Route::get('/popular', [ServiceController::class, 'popular'])->name('popular');
    Route::get('/category/{categorySlug}', [ServiceController::class, 'category'])->name('category');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('show');
});

Route::resource('blog', PostController::class)->only(['index', 'show'])->parameters(['blog' => 'post:slug'])->names(['index' => 'blog.index', 'show' => 'blog.show']);

Route::get('/portfolio', function () {
    $images = Service::with('media')->get()->flatMap(fn($s) => $s->getMedia('before_after')->map(fn($m) => [
        'thumb' => $m->getUrl('thumb'),
        'full' => $m->getUrl(),
        'caption' => $s->name . ' - До/После',
    ]));
    return Inertia::render('Portfolio', ['images' => $images]);
})->name('portfolio');

Route::get('/contact', function () {
    return Inertia::render('Contact', [
        'services' => Service::select('id', 'name')->get(),
    ]);
})->name('contact');

Route::get('/privacy', function () {
    return "Текст политки конфиденциальности";
})->name('privacy');

// Бронирования
Route::prefix('bookings')->middleware(['throttle:10,1'])->name('bookings.')->group(function () {
    Route::get('/create', [BookingController::class, 'create'])->name('create');
    Route::post('/store', [BookingController::class, 'store'])->name('store');
    Route::get('/success/{booking}', [BookingController::class, 'success'])
        ->name('success')
        ->middleware(['signed']);
});

// Отзывы
Route::prefix('reviews')->middleware(['throttle:60,1'])->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('/service/{serviceSlug}', [ReviewController::class, 'serviceReviews'])->name('service');
    Route::post('/', [ReviewController::class, 'store'])->name('store');
});

// SEO routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/sitemap/status', [SitemapController::class, 'status'])->name('sitemap.status');

// Защищенный маршрут для генерации (используйте middleware)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/sitemap/generate', [SitemapController::class, 'generate'])->name('sitemap.generate');
});

// Явно указываем маршруты для Ziggy в блейде
Route::middleware(['web'])->group(function () {
    Route::get('/ziggy', function () {
        return response()->json(
            (new Ziggy('web'))->toArray()
        );
    })->name('ziggy');
});




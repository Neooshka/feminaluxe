<?php

declare(strict_types=1);

use App\Domains\Blog\Http\Controllers\PostController;
use App\Domains\Bookings\Http\Controllers\BookingController;
use App\Domains\Services\Http\Controllers\ServiceController;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Tighten\Ziggy\Ziggy;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/filter', [ServiceController::class, 'filter'])->name('services.filter');
    Route::get('/services/popular', [ServiceController::class, 'popular'])->name('services.popular');
    Route::get('/services/category/{categorySlug}', [ServiceController::class, 'category'])->name('services.category');
    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
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

Route::get('/about', function () {
    return Inertia::render('About', []);
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact', [
        'services' => Service::select('id', 'name')->get(),
    ]);
})->name('contact');

Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('throttle:10,1');

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

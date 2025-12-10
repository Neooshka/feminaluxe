<?php

use App\Domains\Blog\Http\Controllers\PostController;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Api\SitemapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Domains\Services\Http\Controllers\ServiceController;
use App\Domains\Bookings\Http\Controllers\BookingController;

Route::get('/', function () {
    return Inertia::render('Home', [
        'services' => Service::all()->map(fn($s) => [
            'name' => $s->name,
            'description' => Str::limit($s->description, 100),
            'price' => $s->price,
            'images' => $s->getMedia('before_after')->map->getUrl('thumb'),
        ]),
    ])->withViewData(['seo' => ['title' => 'Feminaluxe: Перманентный макияж', 'description' => 'Профессиональные услуги по перманентному макияжу бровей, губ и межресничек']]);  // Интеграция с SEO
});

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/portfolio', function () { return Inertia::render('Portfolio', []); })->name('portfolio');
Route::get('/about', function () { return Inertia::render('About', []); })->name('about');
Route::get('/contact', function () { return Inertia::render('Contact', ['services' => Service::select('id', 'name')->get()]); })->name('contact');  // С формой бронирования
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [PostController::class, 'show'])->name('blog.show');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');  // Для формы

// Sitemap маршруты
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemap/status', [SitemapController::class, 'status']);

// Защищенный маршрут для генерации (используйте middleware)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/sitemap/generate', [SitemapController::class, 'generate']);
});

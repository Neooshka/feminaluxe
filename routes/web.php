<?php

declare(strict_types=1);

use App\Domains\Blog\Http\Controllers\PostController;
use App\Domains\Bookings\Http\Controllers\BookingController;
use App\Domains\Services\Http\Controllers\ServiceController;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Api\SitemapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'services' => Service::with('media')->get()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'description' => Str::limit($s->description, 100),
            'price' => $s->price,
            'images' => $s->getMedia('before_after')->map->getUrl('thumb'),
        ]),
    ])->withViewData([
        'seo' => [
            'title' => 'Feminaluxe: Перманентный макияж',
            'description' => 'Профессиональные услуги по перманентному макияжу бровей, губ и межресничек. Запишитесь онлайн и преобразитесь!',
            'keywords' => 'перманентный макияж, брови, губы, межресничка, красота, Москва',
            'og:title' => 'Feminaluxe - Ваш путь к совершенству',
            'og:description' => 'Естественный макияж на годы. Фото до/после и отзывы клиентов.',
            'og:image' => asset('og-home.jpg'),
        ],
    ]);
})->name('home');

Route::resource('services', ServiceController::class)->only(['index', 'show'])->names(['index' => 'services.index', 'show' => 'services.show']);
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

<?php

use App\Domains\Bookings\Http\Controllers\BookingController;
use App\Domains\Reviews\Http\Controllers\ReviewController;
use App\Domains\Reviews\Models\Review;
use App\Domains\Services\Models\Service;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SitemapController;

Route::prefix('sitemap')->name('api.sitemap.')->group(function () {
    // Основные endpoints
    Route::get('status', [SitemapController::class, 'status'])->name('status');
    Route::post('generate', [SitemapController::class, 'generate'])->name('generate');
    Route::post('clear-cache', [SitemapController::class, 'clearCache'])->name('clear-cache');
    Route::post('validate', [SitemapController::class, 'validateSitemap'])->name('validate');
    Route::post('submit', [SitemapController::class, 'submit'])->name('submit');
    Route::post('backup', [SitemapController::class, 'backup'])->name('backup');
    Route::get('/download-backup/{filename}', [SitemapController::class, 'downloadBackup'])
        ->name('download-backup');

    // Дополнительные endpoints
    Route::get('settings', [SitemapController::class, 'getSettings'])->name('settings.get');
    Route::put('settings', [SitemapController::class, 'updateSettings'])->name('settings.update');
});

Route::prefix('reviews')->name('api.reviews.')->group(function () {
    Route::get('/', fn(Request $request) => Review::approved()->with('service')->latest()->limit($request->limit ?? 10)->get())->name('index');
    Route::get('/service/{service}', [ReviewController::class, 'getServiceReviews'])->name('service');
});

Route::get('/reviews-teaser', fn() => Review::approved()->latest()->take(3)->get())->name('api.reviews-teaser');

Route::get('/booking/check-availability', [BookingController::class, 'checkAvailability'])
    ->name('api.booking.check-availability');

Route::get('/services/list', function () {
    return Service::active()
        ->orderBy('name')
        ->get(['id', 'name', 'slug']);
})->name('api.services.list');

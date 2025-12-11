<?php

use App\Domains\Reviews\Models\Review;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SitemapController;

Route::prefix('sitemap')->name('sitemap.')->group(function () {
    // Основные endpoints
    Route::get('status', [SitemapController::class, 'status'])->name('status');
    Route::post('generate', [SitemapController::class, 'generate'])->name('generate');
    Route::post('clear-cache', [SitemapController::class, 'clearCache'])->name('clear-cache');
    Route::post('validate', [SitemapController::class, 'validateSitemap'])->name('validate');
    Route::post('submit', [SitemapController::class, 'submit'])->name('submit');
    Route::post('backup', [SitemapController::class, 'backup'])->name('backup');

    // Дополнительные endpoints
    Route::get('settings', [SitemapController::class, 'getSettings'])->name('settings.get');
    Route::put('settings', [SitemapController::class, 'updateSettings'])->name('settings.update');
});

// Маршрут для скачивания backup
Route::get('sitemap/download-backup/{filename}', [SitemapController::class, 'downloadBackup'])
    ->name('sitemap.download-backup');

Route::get('/reviews-teaser', fn() => Review::approved()->latest()->take(3)->get())->name('api.reviews-teaser');
Route::get('/reviews', fn(Request $request) => Review::approved()->with('service')->latest()->limit($request->limit ?? 10)->get())->name('api.reviews');

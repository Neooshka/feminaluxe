<?php

use App\Domains\Reviews\Models\Review;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SitemapController;

Route::prefix('sitemap')->name('sitemap.')->group(function () {
    Route::get('status', [SitemapController::class, 'status'])->name('status');
    Route::post('generate', [SitemapController::class, 'generate'])->name('generate');
    Route::post('clear-cache', [SitemapController::class, 'clearCache'])->name('clear-cache');
    Route::put('settings', [SitemapController::class, 'updateSettings'])->name('settings.update');
    Route::post('validate', [SitemapController::class, 'validate'])->name('validate');
    Route::post('submit', [SitemapController::class, 'submit'])->name('submit');
    Route::get('backup', [SitemapController::class, 'backup'])->name('backup');
});

Route::get('/reviews-teaser', fn() => Review::approved()->latest()->take(3)->get())->name('api.reviews-teaser');

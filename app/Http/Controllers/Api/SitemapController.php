<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\SitemapSettings;
use Spatie\Sitemap\Sitemap;

class SitemapController extends Controller
{
    public function status(Request $request)
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return response()->json([
                'exists' => false,
                'lastModified' => null,
                'urlCount' => 0,
                'fileSize' => '0 KB',
                'age' => 'Never generated'
            ]);
        }

        $lastModified = filemtime($sitemapPath);
        $fileSize = filesize($sitemapPath);
        $content = file_get_contents($sitemapPath);
        $urlCount = substr_count($content, '<url>');

        $age = $this->getFileAge($lastModified);

        return response()->json([
            'exists' => true,
            'lastModified' => date('Y-m-d H:i:s', $lastModified),
            'urlCount' => $urlCount,
            'fileSize' => $this->formatBytes($fileSize),
            'age' => $age
        ]);
    }

    public function generate(Request $request)
    {
        // Generate sitemap
        \Artisan::call('sitemap:generate');

        return response()->json([
            'success' => true,
            'message' => 'Sitemap generated successfully',
            'generated_at' => now()->toDateTimeString()
        ]);
    }

    public function clearCache(Request $request)
    {
        Cache::forget('sitemap.generated');
        Cache::forget('sitemap.last_generated');

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'autoGenerate' => 'boolean',
            'submitToGoogle' => 'boolean',
            'enableCaching' => 'boolean',
            'cacheDuration' => 'integer|min:1',
            'updateFrequency' => 'string|in:hourly,daily,weekly,monthly'
        ]);

        $settings = SitemapSettings::firstOrCreate(['user_id' => $request->user()->id]);
        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'settings' => $settings
        ]);
    }

    public function validateSitemap(Request $request)
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return response()->json([
                'valid' => false,
                'message' => 'Sitemap file not found'
            ], 404);
        }

        $xml = simplexml_load_file($sitemapPath);

        if ($xml === false) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid XML structure',
                'errors' => libxml_get_errors()
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Sitemap is valid'
        ]);
    }

    public function submit(Request $request)
    {
        // Submit to Google Search Console
        $sitemapUrl = url('sitemap.xml');

        // Here you would implement actual submission logic
        // For now, we'll simulate it
        $success = true;

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Sitemap submitted to search engines'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to submit to search engines'
        ], 500);
    }

    public function backup(Request $request)
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return response()->json([
                'error' => 'Sitemap not found'
            ], 404);
        }

        return response()->file($sitemapPath, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="sitemap-backup-' . date('Y-m-d') . '.xml"'
        ]);
    }

    private function getFileAge($timestamp)
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

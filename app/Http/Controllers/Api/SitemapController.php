<?php

namespace App\Http\Controllers\Api;

use App\Domains\Blog\Models\Post;
use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\Sitemap;

class SitemapController extends Controller
{
    public function index()
    {
        $services = Service::active()->get('slug')->map(function ($service) {
            return '/services/' . $service->slug;
        });
        
        $portfolio = Portfolio::published()->get('slug')->map(function ($portfolio) {
            return '/portfolio/' . $portfolio->slug;
        });
        
        $blog = Post::published()->get('slug')->map(function ($post) {
            return '/blog/' . $post->slug;
        });
        
        $sitemap = Sitemap::create()
            ->add(url('/'))
            ->add(url('/services'))
            ->add(url('/portfolio'))
            ->add(url('/reviews'))
            ->add(url('/blog'))
            ->add(url('/contact'))
            ->add(url('/bookings/create'))
            ->add(url('/privacy'))
            ->add(url('/agreement'))
            ->add($services)
            ->add($portfolio)
            ->add($blog);
        return response($sitemap->render())->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml'), 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Получить статус sitemap
     */
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

    /**
     * Сгенерировать sitemap
     */
    public function generate(Request $request)
    {
        try {
            // Выполняем команду генерации sitemap
            Artisan::call('sitemap:generate');

            // Проверяем, был ли создан файл
            $sitemapPath = public_path('sitemap.xml');
            $exists = file_exists($sitemapPath);

            return response()->json([
                'success' => true,
                'message' => 'Sitemap generated successfully',
                'generated_at' => now()->toDateTimeString(),
                'file_exists' => $exists
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap: ' . $e->getMessage(),
                'generated_at' => null
            ], 500);
        }
    }

    /**
     * Очистить кэш sitemap
     */
    public function clearCache(Request $request)
    {
        try {
            // Очищаем кэш sitemap (если используется)
            Cache::forget('sitemap.generated');
            Cache::forget('sitemap.last_generated');

            // Также можно очистить кэш приложения
            Artisan::call('cache:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить настройки (сохраняем в конфиге или кэше)
     */
    public function updateSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'autoGenerate' => 'boolean',
                'submitToGoogle' => 'boolean',
                'enableCaching' => 'boolean',
                'cacheDuration' => 'integer|min:1',
                'updateFrequency' => 'string|in:hourly,daily,weekly,monthly'
            ]);

            // Сохраняем настройки в кэше (временное решение)
            // В реальном проекте можно сохранять в файле конфигурации или БД
            Cache::put('sitemap_settings', $validated, now()->addDays(30));

            // Обновляем расписание в Laravel
            $this->updateSchedule($validated['updateFrequency']);

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'settings' => $validated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить настройки
     */
    public function getSettings(Request $request)
    {
        // Получаем настройки из кэша или возвращаем значения по умолчанию
        $settings = Cache::get('sitemap_settings', [
            'autoGenerate' => true,
            'submitToGoogle' => false,
            'enableCaching' => true,
            'cacheDuration' => 60,
            'updateFrequency' => 'daily'
        ]);

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Проверить валидность sitemap
     */
    public function validateSitemap(Request $request)
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return response()->json([
                'valid' => false,
                'message' => 'Sitemap file not found',
                'errors' => ['File does not exist']
            ]);
        }

        try {
            // Простая проверка XML
            $xml = simplexml_load_file($sitemapPath);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();

                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid XML structure',
                    'errors' => array_map(function($error) {
                        return $error->message;
                    }, $errors)
                ]);
            }

            // Проверяем обязательные элементы
            $hasUrls = isset($xml->url);
            $hasUrlset = $xml->getName() === 'urlset';

            return response()->json([
                'valid' => true,
                'message' => 'Sitemap is valid',
                'hasUrls' => $hasUrls,
                'hasUrlset' => $hasUrlset,
                'urlCount' => count($xml->url)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Отправить sitemap в поисковые системы
     */
    public function submit(Request $request)
    {
        try {
            $sitemapUrl = url('sitemap.xml');

            // Отправка в Google (Ping)
            $googleResponse = $this->pingGoogle($sitemapUrl);

            // Отправка в Bing
            $bingResponse = $this->pingBing($sitemapUrl);

            return response()->json([
                'success' => true,
                'message' => 'Sitemap submitted to search engines',
                'google' => $googleResponse,
                'bing' => $bingResponse,
                'sitemap_url' => $sitemapUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit sitemap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачать backup sitemap
     */
    public function backup(Request $request)
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return response()->json([
                'error' => 'Sitemap file not found'
            ], 404);
        }

        // Создаем backup директорию если её нет
        $backupDir = storage_path('app/sitemap-backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Копируем файл в backup директорию
        $backupName = 'sitemap-backup-' . date('Y-m-d-His') . '.xml';
        $backupPath = $backupDir . '/' . $backupName;
        File::copy($sitemapPath, $backupPath);

        return response()->json([
            'success' => true,
            'message' => 'Backup created successfully',
            'backup_path' => $backupPath,
            'backup_name' => $backupName,
            'backup_url' => route('sitemap.download-backup', ['file' => $backupName])
        ]);
    }

    /**
     * Скачать файл backup
     */
    public function downloadBackup($filename)
    {
        $backupPath = storage_path('app/sitemap-backups/' . $filename);

        if (!File::exists($backupPath)) {
            abort(404, 'Backup file not found');
        }

        return response()->download($backupPath, $filename);
    }

    /**
     * Пинг Google
     */
    private function pingGoogle($sitemapUrl)
    {
        $pingUrl = 'https://www.google.com/ping?sitemap=' . urlencode($sitemapUrl);

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($pingUrl);

            return [
                'success' => $response->getStatusCode() === 200,
                'status' => $response->getStatusCode(),
                'message' => 'Successfully pinged Google'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Пинг Bing
     */
    private function pingBing($sitemapUrl)
    {
        $pingUrl = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemapUrl);

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($pingUrl);

            return [
                'success' => $response->getStatusCode() === 200,
                'status' => $response->getStatusCode(),
                'message' => 'Successfully pinged Bing'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Обновить расписание
     */
    private function updateSchedule($frequency)
    {
        // Здесь можно обновить расписание в database/jobs или другом месте
        // Для простоты просто возвращаем true
        return true;
    }

    /**
     * Форматировать возраст файла
     */
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

    /**
     * Форматировать размер файла
     */
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonitorSitemap extends Command
{
    protected $signature = 'sitemap:monitor';

    public function handle()
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            $this->error('Sitemap не найден! Запустите sitemap:generate');
            return;
        }

        $lastModified = filemtime($sitemapPath);
        $age = time() - $lastModified;

        if ($age > 86400) { // Старше 24 часов
            $this->warn('Sitemap устарел (более 24 часов). Запустите sitemap:generate');
        } else {
            $this->info('Sitemap актуален');
        }

        // Проверка валидности XML
        $xml = simplexml_load_file($sitemapPath);
        if ($xml === false) {
            $this->error('Sitemap содержит ошибки XML! Запустите sitemap:generate');
        }
    }
}

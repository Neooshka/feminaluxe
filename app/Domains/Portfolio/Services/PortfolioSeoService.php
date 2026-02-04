<?php

declare(strict_types=1);

namespace App\Domains\Portfolio\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class PortfolioSeoService
{
    public function generateIndexSeoData(): SEOData
    {
        return new SEOData(
            title: 'Портфолио работ по перманентному макияжу | Фото до и после | Feminaluxe',
            description: '✅ Реальные фото работ до и после перманентного макияжа. Портфолио наших мастеров: татуаж бровей, губ, межресничный эффект. Галерея результатов.',
            author: 'Feminaluxe Studio',
            image: asset('img/seo/portfolio-og.jpg'),
            published_time: Carbon::now(),
            modified_time: Carbon::now(),
            robots: 'index, follow',
            canonical_url: route('portfolio'),
            openGraphTitle: 'Портфолио работ по перманентному макияжу | Feminaluxe'
        );
    }
}
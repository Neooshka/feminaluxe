<?php

declare(strict_types=1);

namespace App\Domains\Services\Services;

use App\Domains\Services\Data\ServiceShowData;
use Carbon\Carbon;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ServiceSeoService
{
    public function generateIndexSeoData(): SEOData
    {
        return new SEOData(
            title: $this->getIndexTitle(),
            description: $this->getIndexDescription(),
            author: 'Feminaluxe Studio',
            image: asset('img/seo/services-og.jpg'),
            published_time: Carbon::now(),
            modified_time: Carbon::now(),
            schema: $this->generateServiceCollectionSchema(),
            robots: 'index, follow, max-image-preview:large',
            canonical_url: route('services.index'),
            openGraphTitle: 'Услуги перманентного макияжа премиум-класса | Feminaluxe'
        );
    }

    /**
     * Generate generic SEO data
     */
    public function generateSeoData(
        string $title,
        string $description,
        ?string $image = null,
        ?string $canonical = null,
        ?Carbon $publishedTime = null,
        ?Carbon $modifiedTime = null
    ): SEOData {
        $seoData = new SEOData(
            title: $title,
            description: $description,
            author: 'Feminaluxe Studio',
            image: $image ?? asset('img/seo/default-og.jpg'),
            published_time: $publishedTime ?? Carbon::now(),
            modified_time: $modifiedTime ?? Carbon::now(),
            robots: 'index, follow',
            canonical_url: $canonical ?? url()->current(),
        );

        $seoData->openGraphTitle = $title;

        return $seoData;
    }
    public function generateShowSeoData(array $service): SEOData
    {
        return new SEOData(
            title: $this->getShowTitle($service),
            description: $this->getShowDescription($service),
            author: 'Feminaluxe Studio',
            image: $service['primary_image_url'] ?? asset('img/seo/default-og.jpg'),
            published_time: Carbon::parse($service['created_at'] ?? now()),
            modified_time: Carbon::parse($service['updated_at'] ?? now()),
            robots: 'index, follow',
            canonical_url: route('services.show', $service['slug']),
            openGraphTitle: $service['name'] . ' | Feminaluxe' // Will be added via structuredData in controller
        );
    }

    public function getIndexTitle(): string
    {
        return 'Услуги перманентного макияжа бровей, губ и межресничек в Балашихе | Feminaluxe';
    }

    public function getIndexDescription(): string
    {
        return '✨ Премиум услуги перманентного макияжа: пудровые брови, акварельные губы, межресничный эффект. Используем гипоаллергенные пигменты премиум-класса. Запишитесь онлайн на бесплатную консультацию!';
    }

    public function getIndexKeywords(): array
    {
        return [
            'перманентный макияж',
            'татуаж бровей',
            'татуаж губ',
            'межресничный перманент',
            'пудровые брови Балашиха',
            'акварельные губы',
            'коррекция татуажа',
            'удаление татуажа лазером',
            'цена перманентного макияжа',
        ];
    }

    public function getShowTitle(array $service): string
    {
        return sprintf('%s - цена %s руб. | Перманентный макияж в Балашихе | Feminaluxe',
            $service['name'],
            number_format($service['final_price'], 0, '', ' ')
        );
    }

    public function getShowDescription(array $service): string
    {
        $baseDescription = Str::limit(strip_tags($service['description'] ?? $service['short_description'] ?? ''), 120);

        return sprintf('%s Цена: %s руб. ✅ Гарантия результата ✅ Используем сертифицированные пигменты ✅ Бесплатная коррекция через месяц. Запишитесь онлайн!',
            $baseDescription,
            number_format($service['final_price'], 0, '', ' ')
        );
    }

    public function generateServiceStructuredData(array $service): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'serviceType' => 'BeautyService',
            'provider' => [
                '@type' => 'BeautySalon',
                'name' => 'Feminaluxe',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Балашиха',
                    'postalCode' => '143914',
                    'streetAddress' => config('app.contact.address'),
                ],
                'telephone' => config('app.contact.phone'),
                'priceRange' => '$$$',
            ],
            'name' => $service['name'],
            'description' => strip_tags($service['short_description'] ?? ''),
            'offers' => [
                '@type' => 'Offer',
                'price' => $service['final_price'],
                'priceCurrency' => 'RUB',
                'availability' => 'https://schema.org/InStock',
                'validFrom' => Carbon::parse($service['created_at'] ?? now())->toIso8601String(),
            ],
        ];

        if (($service['rating'] ?? 0) > 0) {
            $structuredData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $service['rating'],
                'reviewCount' => $service['reviews_count'],
            ];
        }

        return $structuredData;
    }

    private function generateServiceCollectionSchema(): SchemaCollection
    {
        return SchemaCollection::make()
            ->add(fn(SEOData $SEOData) => [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'itemListElement' => [],
                'name' => 'Услуги перманентного макияжа',
                'description' => 'Полный перечень услуг по перманентному макияжу в салоне Feminaluxe',
            ]);
    }
}

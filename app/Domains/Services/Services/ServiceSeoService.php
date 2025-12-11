<?php

declare(strict_types=1);

namespace App\Domains\Services\Services;

use App\Domains\Services\Data\ServiceShowData;
use Carbon\Carbon;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ServiceSeoService
{
    public function generateIndexSeoData(): SEOData
    {
        return new SEOData(
            title: $this->getIndexTitle(),
            description: $this->getIndexDescription(),
            image: asset('img/seo/services-og.jpg'),
            published_time: Carbon::now(),
            modified_time: Carbon::now(),
            openGraphTitle: 'Услуги перманентного макияжа премиум-класса | Feminaluxe',
            twitterTitle: 'Элитный перманентный макияж в Москве | Feminaluxe',
            author: 'Feminaluxe Studio',
            robots: 'index, follow, max-image-preview:large',
            canonical: route('services.index'),
            schema: $this->generateServiceCollectionSchema()
        );
    }

    public function generateShowSeoData(ServiceShowData $service): SEOData
    {
        return new SEOData(
            title: $this->getShowTitle($service),
            description: $this->getShowDescription($service),
            image: $service->primary_image ?: asset('img/seo/default-og.jpg'),
            published_time: $service->created_at,
            modified_time: $service->updated_at,
            openGraphTitle: $service->name . ' | Feminaluxe',
            twitterTitle: $service->name . ' - Перманентный макияж',
            author: 'Feminaluxe Studio',
            robots: 'index, follow',
            canonical: route('services.show', $service->slug),
            schema: null // Will be added via structuredData in controller
        );
    }

    public function getIndexTitle(): string
    {
        return 'Услуги перманентного макияжа бровей, губ и межресничек в Москве | Feminaluxe';
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
            'пудровые брови Москва',
            'акварельные губы',
            'коррекция татуажа',
            'удаление татуажа лазером',
            'цена перманентного макияжа',
        ];
    }

    public function getShowTitle(ServiceShowData $service): string
    {
        return sprintf('%s - цена %s руб. | Перманентный макияж в Москве | Feminaluxe',
            $service->name,
            number_format($service->price, 0, '', ' ')
        );
    }

    public function getShowDescription(ServiceShowData $service): string
    {
        $baseDescription = Str::limit(strip_tags($service->description), 120);

        return sprintf('%s Цена: %s руб. ✅ Гарантия результата ✅ Используем сертифицированные пигменты ✅ Бесплатная коррекция через месяц. Запишитесь онлайн!',
            $baseDescription,
            number_format($service->price, 0, '', ' ')
        );
    }

    public function generateServiceStructuredData(ServiceShowData $service): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'serviceType' => 'BeautyService',
            'provider' => [
                '@type' => 'BeautySalon',
                'name' => 'Feminaluxe',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Москва',
                    'postalCode' => '123456',
                    'streetAddress' => 'ул. Примерная, д. 1',
                ],
                'telephone' => '+7 (495) 123-45-67',
                'priceRange' => '$$$',
            ],
            'name' => $service->name,
            'description' => strip_tags($service->short_description),
            'offers' => [
                '@type' => 'Offer',
                'price' => $service->price,
                'priceCurrency' => 'RUB',
                'availability' => 'https://schema.org/InStock',
                'validFrom' => $service->created_at->toIso8601String(),
            ],
            'aggregateRating' => $service->rating > 0 ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $service->rating,
                'reviewCount' => $service->reviews_count,
            ] : null,
        ];
    }

    private function generateServiceCollectionSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => [],
            'name' => 'Услуги перманентного макияжа',
            'description' => 'Полный перечень услуг по перманентному макияжу в салоне Feminaluxe',
        ];
    }
}

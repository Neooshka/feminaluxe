<?php

namespace Database\Seeders;

use App\Domains\Services\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Генерируем slug для существующих записей
        $services = Service::all();

        foreach ($services as $service) {
            // Генерация уникального slug
            $slugBase = Str::slug($service->name);
            $slug = $slugBase;
            $counter = 1;

            while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug = $slugBase . '-' . $counter;
                $counter++;
            }

            // Заполняем недостающие поля
            $service->update([
                'slug' => $slug,
                'short_description' => $this->generateShortDescription($service),
                'detailed_description' => $service->description,
                'duration' => $this->getDurationByCategory($service->category),
                'sort_order' => $this->getSortOrderByCategory($service),
                'benefits' => $this->getBenefitsByCategory($service->category),
                'contraindications' => $this->getContraindications(),
                'meta_title' => $service->name . ' | Feminaluxe - Перманентный макияж',
                'meta_description' => $this->generateMetaDescription($service),
                'published_at' => now(),
            ]);
        }

        // Помечаем некоторые услуги как популярные
        Service::inRandomOrder()->limit(3)->update(['is_popular' => true]);

        // Добавляем скидки для некоторых услуг
        $discountedServices = Service::inRandomOrder()->limit(2)->get();
        foreach ($discountedServices as $service) {
            $service->update([
                'has_promotion' => true,
                'discounted_price' => $service->price * 0.85, // 15% скидка
                'featured_until' => now()->addDays(30),
            ]);
        }
    }

    private function generateShortDescription(Service $service): string
    {
        $descriptions = [
            'Брови' => 'Профессиональный перманентный макияж бровей с эффектом пудрового напыления.',
            'Губы' => 'Акварельный татуаж губ для естественного и стойкого результата.',
            'Межресничка' => 'Межресничный перманент для выразительного взгляда без подводки.',
        ];

        return $descriptions[$service->category] ?? 'Профессиональная услуга перманентного макияжа.';
    }

    private function getDurationByCategory(?string $category): string
    {
        return match($category) {
            'Брови' => '2-3 часа',
            'Губы' => '3-4 часа',
            'Межресничка' => '1.5-2 часа',
            default => '2-3 часа',
        };
    }

    private function getSortOrderByCategory(Service $service): int
    {
        $orderMap = [
            'Брови' => [100, 200, 300],
            'Губы' => [400, 500, 600],
            'Межресничка' => [700, 800, 900],
        ];

        $orders = $orderMap[$service->category] ?? [1000, 1100, 1200];

        static $counter = 0;
        return $orders[$counter++ % count($orders)] ?? 1000;
    }

    private function getBenefitsByCategory(?string $category): array
    {
        $commonBenefits = [
            'Стойкость до 1.5 лет',
            'Безболезненная процедура',
            'Используем гипоаллергенные пигменты',
            'Индивидуальный подбор цвета',
            'Бесплатная коррекция через 1 месяц',
        ];

        $specificBenefits = match($category) {
            'Брови' => [
                'Коррекция формы бровей',
                'Маскировка шрамов и проплешин',
                'Эффект естественных волосков',
            ],
            'Губы' => [
                'Коррекция асимметрии',
                'Увеличение объема',
                'Четкий контур',
            ],
            'Межресничка' => [
                'Утолщение линии ресниц',
                'Естественный эффект',
                'Без повреждения ресниц',
            ],
            default => [],
        };

        return array_merge($commonBenefits, $specificBenefits);
    }

    private function getContraindications(): array
    {
        return [
            'Беременность и период лактации',
            'Сахарный диабет в стадии декомпенсации',
            'Онкологические заболевания',
            'Кожные заболевания в зоне процедуры',
            'Склонность к келоидным рубцам',
            'Острая фаза герпеса',
            'Прием антикоагулянтов',
        ];
    }

    private function generateMetaDescription(Service $service): string
    {
        return sprintf(
            '%s по доступной цене. Профессиональный перманентный макияж в Балашихе. ✅ Гарантия качества ✅ Опытные мастера ✅ Запишитесь онлайн!',
            $service->name
        );
    }
}

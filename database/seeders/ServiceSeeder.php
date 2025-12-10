<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Services\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::factory()->create([
            'name' => 'Перманент бровей',
            'description' => 'Естественный вид бровей на 1-2 года. Техника пудрового напыления.',
            'price' => 5000.00,
        ]);

        Service::factory()->create([
            'name' => 'Перманент губ',
            'description' => 'Контур и заполнение для идеальной формы губ.',
            'price' => 6000.00,
        ]);

        Service::factory()->create([
            'name' => 'Межресничный перманент',
            'description' => 'Акцент на глаза без ежедневного макияжа.',
            'price' => 4000.00,
        ]);
    }
}

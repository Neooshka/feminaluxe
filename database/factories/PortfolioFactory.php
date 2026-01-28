<?php

namespace Database\Factories;

use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Portfolio\Models\Portfolio>
 */
class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);
        return [
            'service_id' => fn () => Service::inRandomOrder()->first()?->id ?? Service::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->text(500),
            'is_published' => true,
            'published_at' => now(),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}

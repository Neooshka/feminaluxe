<?php

namespace Database\Factories;

use App\Domains\Reviews\Models\Review;
use App\Domains\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Reviews\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => fn () => Service::inRandomOrder()->first()?->id,
            'author' => $this->faker->name(),
            'content' => $this->faker->realText(300),
            'rating' => $this->faker->numberBetween(1, 5),
            'approved' => $this->faker->boolean(80),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}

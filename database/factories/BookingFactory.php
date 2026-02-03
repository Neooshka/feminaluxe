<?php

namespace Database\Factories;

use App\Domains\Bookings\Models\Booking;
use App\Domains\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Bookings\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => fn () => Service::inRandomOrder()->first()?->id,
            'client_name' => $this->faker->name(),
            'client_email' => $this->faker->safeEmail(),
            'client_phone' => $this->faker->phoneNumber(),
            'appointment_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'source' => $this->faker->randomElement(['site', 'instagram', 'phone']),
            'utm_source' => $this->faker->randomElement(['google', 'yandex', 'fb']),
            'utm_medium' => 'cpc',
            'utm_campaign' => 'summer_promo',
        ];
    }
}

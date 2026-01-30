<?php

namespace Database\Factories;

use App\Domains\Blog\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Blog\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence();
        return [
            'title' => $title,
            'slug' => Str::random(25),
            'content' => $this->faker->paragraphs(5, true),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

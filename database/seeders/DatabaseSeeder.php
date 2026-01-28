<?php

namespace Database\Seeders;

use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Models\Tag;
use App\Domains\Bookings\Models\Booking;
use App\Domains\Portfolio\Models\Portfolio;
use App\Domains\Reviews\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        DB::transaction(function () {
            // Создаем администратора
            User::firstOrCreate(
                ['email' => 'neeeo@mail.ru'],
                [
                    'name' => 'Дмитрий',
                    'password' => bcrypt('123Ek147'),
                    'role' => 'admin',
                    'is_active' => true,
                ]
            );

            // Создаем бронирования
            Booking::factory()->count(5)->create();

            // Создаем отзывы
            Review::factory()->count(5)->create();

            // Создаем теги
            $tags = Tag::factory()->count(5)->create();

            // Создаем статьи блога
            $posts = Post::factory()->count(5)->create();

            // Привязываем теги к статьям
            $posts->each(function ($post) use ($tags) {
                $post->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
            });

            // Создаем портфолио
            Portfolio::factory()->count(5)->create();
            
            DB::commit();
        });
    }
}
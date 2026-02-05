<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ContentManager
{
    public static function getMasterData(): array
    {
        $masterDir = 'masters/anna';
        $storage = Storage::disk('public');

        // Получаем все фото кроме аватара
        $allFiles = $storage->files($masterDir);
        $photos = collect($allFiles)
            ->filter(fn($file) => !str_contains(basename($file), 'avatar'))
            ->map(fn($file) => Storage::url($file))
            ->values()
            ->toArray();

        // Аватар
        $avatar = collect($allFiles)
            ->first(fn($file) => str_contains(basename($file), 'avatar'));

        return [
            'name' => 'Анна Кузнецова',
            'title' => 'Профессионал с медицинским образованием и многолетним опытом работы',
            'experience' => 5,
            'procedures_count' => 500,
            'rating' => 5.0,
            'description' => 'Опыт работы 5+ лет. Специализация: пудровые брови, микроблейдинг, акварельные губы, межресничный перманент',
            'photos' => $photos,
            'avatar' => $avatar ? Storage::url($avatar) : null,
            'total_photos' => count($photos),
        ];
    }
}
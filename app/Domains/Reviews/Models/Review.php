<?php

namespace App\Domains\Reviews\Models;

use App\Domains\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['author', 'content', 'rating', 'approved'];

    protected $casts = [
        'approved' => 'boolean',
        'rating' => 'integer',
    ];

    public function service()
    {
        return $this->hasOne(Service::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before_after')
            ->useFallbackUrl('/fallback.jpg');
    }
}

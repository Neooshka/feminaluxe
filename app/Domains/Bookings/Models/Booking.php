<?php

namespace App\Domains\Bookings\Models;

use App\Domains\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Booking extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['client_name', 'client_email', 'client_phone', 'appointment_time', 'status'];

    protected $casts = [
        'appointment_time' => 'datetime',
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

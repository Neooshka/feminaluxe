<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'author',
        'content',
        'rating',
        'approved',
        'email',
        'phone',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'rating' => 'integer',
        'approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Services\Models\Service::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    // Новые методы
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('content', 'like', '%' . $search . '%')
              ->orWhere('author', 'like', '%' . $search . '%');
        });
    }
}

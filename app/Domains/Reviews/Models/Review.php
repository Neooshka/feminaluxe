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
    ];

    protected $casts = [
        'rating' => 'integer',
        'approved' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Services\Models\Service::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }
}

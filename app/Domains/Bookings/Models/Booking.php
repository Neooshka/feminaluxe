<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Models;

use App\Domains\Services\Models\Service;
use App\Events\BookingCreated;
use App\Events\BookingUpdated;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'client_name',
        'client_email',
        'client_phone',
        'appointment_time',
        'status',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'message',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
        'status' => 'string',
        'deleted_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => BookingCreated::class,
        'updated' => BookingUpdated::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('appointment_time', $date);
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function cancel(): void
    {
    $this->update(['status' => 'cancelled']);
    }
    
    protected static function newFactory()
    {
        return BookingFactory::new();
    }
}
<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Models;

use App\Domains\Services\Models\Service;
use App\Events\BookingCreated;
use App\Events\BookingUpdated;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Event;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public mixed $status;
    protected $fillable = [
        'service_id',
        'client_name',
        'client_email',
        'client_phone',
        'appointment_time',
        'status',
    ];

    protected $casts = [
        'appointment_time' => 'datetime:Y-m-d H:i:s',  // Custom format for JSON/API
        'status' => 'string',
        'deleted_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => BookingCreated::class,  // For notifications/analytics
        'updated' => BookingUpdated::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Scopes for efficient queries
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

    // Custom method for business logic
    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
        // Trigger email or SMS via queue
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Bookings\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class SendBookingSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function handle(): void
    {
        $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $message = "Ваша запись в Feminaluxe на {$this->booking->service->name} подтверждена. Дата: {$this->booking->appointment_time->format('d.m.Y H:i')}. Ждем вас!";

        $client->messages->create(
            $this->booking->client_phone,
            [
                'from' => config('services.twilio.from'),
                'body' => $message,
            ]
        );
    }
}

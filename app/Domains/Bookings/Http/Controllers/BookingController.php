<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Http\Controllers;

use App\Domains\Bookings\Models\Booking;
use App\Http\Controllers\Controller;
use App\Jobs\SendBookingSms;
use App\Mail\BookingConfirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'appointment_time' => ['required', 'date', 'after:now'],
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ]);

        $booking = Booking::create($validated);

        // Отправка СМС
//        SendBookingSms::dispatch($booking);

        // Emails queued via event listener now (better separation)

        return redirect()->back()->with('success', 'Запись успешно создана!');
    }

    // Additional methods for admin if needed, e.g., index for API
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Booking::with('service')->pending()->get());
    }
}

<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Http\Controllers;

use App\Domains\Bookings\Models\Booking;
use App\Domains\Services\Models\Service;
use App\Domains\Services\Services\ServiceCacheService;
use App\Http\Controllers\Controller;
use App\Jobs\SendBookingSms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly ServiceCacheService $cacheService
    ) {}

    /**
     * Show booking modal with service pre-selection
     */
    public function create(Request $request): Response
    {
        $request->session()->regenerate();

        $service = null;
        $services = [];

        // Если передан slug услуги - предзаполняем её
        if ($request->has('service_slug')) {
            $cacheKey = 'booking_service_' . $request->input('service_slug');
            $service = Cache::remember($cacheKey, 3600, function () use ($request) {
                return Service::query()
                    ->where('slug', $request->input('service_slug'))
                    ->active()
                    ->first();
            });
        }

        // Получаем все активные услуги для выпадающего списка
        $services = $this->cacheService->remember(
            'booking_services_list',
            ['services.booking'],
            1800,
            function () {
                return Service::query()
                    ->active()
                    ->orderBy('category')
                    ->orderBy('name')
                    ->get(['id', 'slug', 'name', 'category', 'price', 'discounted_price', 'duration', 'has_promotion'])
                    ->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'slug' => $service->slug,
                            'name' => $service->name,
                            'category' => $service->category,
                            'price' => $service->price,
                            'final_price' => $service->has_promotion ? $service->discounted_price : $service->price,
                            'duration' => $service->duration,
                        ];
                    })
                    ->toArray();
            }
        );

        // Получаем доступные временные слоты (следующие 14 дней, кроме выходных)
        $availableSlots = $this->generateAvailableTimeSlots();

        return Inertia::render('Booking', [
            'prefilledService' => $service,
            'services' => $services,
            'availableSlots' => $availableSlots,
            'csrf_token' => csrf_token(),
            'meta' => [
                'title' => 'Запись на консультацию | Feminaluxe',
                'description' => 'Запишитесь на бесплатную консультацию по перманентному макияжу. Подберем удобное время и ответим на все вопросы.',
            ]
        ]);
    }

    /**
     * Store booking with validation and notifications
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBookingRequest($request);

        // Объединяем дату и время
        $appointmentDateTime = \Carbon\Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['appointment_date'] . ' ' . $validated['appointment_time']
        );

        // Проверяем, что время не в прошлом
        if ($appointmentDateTime->isPast()) {
            return back()->withErrors([
                'appointment_time' => 'Выбранное время уже прошло. Пожалуйста, выберите другое время.'
            ]);
        }

        // Проверяем доступность времени (можно добавить проверку занятых слотов)
        $existingBooking = Booking::where('appointment_time', $appointmentDateTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($existingBooking) {
            return back()->withErrors([
                'appointment_time' => 'Это время уже занято. Пожалуйста, выберите другое время.'
            ]);
        }

        // Создаем запись
        $booking = Booking::create([
            'service_id' => $validated['service_id'],
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'],
            'client_phone' => $this->formatPhone($validated['client_phone']),
            'appointment_time' => $appointmentDateTime,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
            'source' => $validated['source'] ?? 'booking_page',
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
        ]);

        // Сохраняем ID брони в сессии для страницы успеха
        $request->session()->put('booking_id', $booking->id);

        // Инвалидируем кэш бронирований
        $this->cacheService->invalidateService($booking->service->slug ?? '');

        // Отправляем уведомления (в очередь)
        // SendBookingSms::dispatch($booking);
        // Mail::to($booking->client_email)->queue(new BookingConfirmation($booking));

        // Отправляем администратору
        // Mail::to(config('mail.admin_email'))->queue(new BookingNotification($booking));

        // Трекаем событие для аналитики
        if (config('services.analytics.enabled')) {
            $this->trackBookingEvent($booking);
        }

        return redirect()->route('bookings.success', [
            'booking' => $booking->id,
            'token' => sha1($booking->id . $booking->created_at)
        ]);
    }

    /**
     * Custom validation for booking request
     */
    private function validateBookingRequest(Request $request): array
    {
        return $request->validate([
            'service_id' => [
                'required',
                'exists:services,id',
                function ($attribute, $value, $fail) {
                    $service = Service::find($value);
                    if (!$service || !$service->is_active) {
                        $fail('Выбранная услуга недоступна.');
                    }
                }
            ],
            'client_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u'
            ],
            'client_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Простая проверка на disposable email
                    $disposableDomains = ['tempmail.com', '10minutemail.com', 'guerrillamail.com'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (in_array($domain, $disposableDomains)) {
                        $fail('Пожалуйста, используйте постоянный email адрес.');
                    }
                }
            ],
            'client_phone' => [
                'required',
                'string',
                'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
            ],
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    // Не разрешаем запись на сегодня если уже поздно
                    if ($value === now()->format('Y-m-d') && now()->hour >= 18) {
                        $fail('На сегодня запись уже закрыта. Пожалуйста, выберите другой день.');
                    }
                }
            ],
            'appointment_time' => ['required', 'date_format:H:i'],
            'message' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'string'],
            'utm_source' => ['nullable', 'string'],
            'utm_medium' => ['nullable', 'string'],
            'utm_campaign' => ['nullable', 'string'],
        ], [
            'client_name.regex' => 'Имя может содержать только буквы, пробелы и дефисы.',
            'client_phone.regex' => 'Введите корректный номер телефона.',
            'appointment_date.after_or_equal' => 'Дата не может быть в прошлом.',
        ]);
    }

    /**
     * Success page after booking
     */
    public function success(Request $request, Booking $booking): Response
    {
        // Проверяем, что пользователь имеет доступ к этой брони
        if ($request->session()->get('booking_id') !== $booking->id &&
            !$request->hasValidSignature()) {
            abort(403);
        }

        return Inertia::render('Booking/Success', [
            'booking' => $booking->load('service'),
            'meta' => [
                'title' => 'Запись успешно создана! | Feminaluxe',
                'description' => 'Ваша запись на консультацию успешно создана. Мы свяжемся с вами для подтверждения.',
            ]
        ]);
    }

    /**
     * Generate available time slots for next 14 days
     */
    private function generateAvailableTimeSlots(): array
    {
        $slots = [];
        $startDate = now()->addDay(); // Начинаем с завтра
        $endDate = now()->addDays(14);

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Пропускаем выходные
            if (!$currentDate->isWeekend()) {
                $dateSlots = [];

                // Время работы: с 10:00 до 20:00, слоты по 60 минут
                $startTime = $currentDate->copy()->setTime(10, 0);
                $endTime = $currentDate->copy()->setTime(20, 0);

                while ($startTime->lt($endTime)) {
                    $slotTime = $startTime->format('H:i');
                    $dateSlots[] = [
                        'time' => $slotTime,
                        'available' => $this->isSlotAvailable($currentDate->format('Y-m-d'), $slotTime),
                    ];
                    $startTime->addHour();
                }

                $slots[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'display_date' => $currentDate->translatedFormat('d F Y'),
                    'weekday' => $currentDate->translatedFormat('l'),
                    'slots' => $dateSlots,
                ];
            }

            $currentDate->addDay();
        }

        return $slots;
    }

    /**
     * Check if time slot is available (можно добавить проверку занятых слотов)
     */
    private function isSlotAvailable(string $date, string $time): bool
    {
        $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

        // Если время уже прошло
        if ($dateTime->isPast()) {
            return false;
        }

        // Проверяем занятость в базе данных (можно добавить кэширование)
        $isBooked = Booking::whereDate('appointment_time', $date)
            ->whereTime('appointment_time', $time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        return !$isBooked;
    }

    /**
     * Format phone number to standard format
     */
    private function formatPhone(string $phone): string
    {
        // Удаляем все нецифровые символы
        $digits = preg_replace('/\D/', '', $phone);

        // Если начинается с 8, заменяем на +7
        if (str_starts_with($digits, '8')) {
            $digits = '7' . substr($digits, 1);
        }

        // Форматируем: +7 (XXX) XXX-XX-XX
        if (strlen($digits) === 11) {
            return sprintf(
                '+7 (%s) %s-%s-%s',
                substr($digits, 1, 3),
                substr($digits, 4, 3),
                substr($digits, 7, 2),
                substr($digits, 9, 2)
            );
        }

        return $phone;
    }

    /**
     * Track booking event for analytics
     */
    private function trackBookingEvent(Booking $booking): void
    {
        // Здесь можно интегрировать с Google Analytics, Yandex.Metrica и т.д.
        // Например:
        // \Analytics::trackEvent('booking_created', [
        //     'service_id' => $booking->service_id,
        //     'service_name' => $booking->service->name,
        //     'price' => $booking->service->price,
        //     'source' => $booking->source,
        // ]);
    }

    /**
     * API endpoint for checking slot availability
     */
    public function checkAvailability(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $isAvailable = $this->isSlotAvailable($request->date, $request->time);

        return response()->json([
            'available' => $isAvailable,
            'date' => $request->date,
            'time' => $request->time,
        ]);
    }
}

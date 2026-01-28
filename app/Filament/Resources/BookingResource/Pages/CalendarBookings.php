<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Domains\Bookings\Models\Booking;
use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;

class CalendarBookings extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.resources.booking-resource.pages.calendar-bookings';

    public $currentDate;
    public $selectedDate;
    public $viewType = 'week'; // week, day, month
    public $timeSlots = [];

    protected $queryString = [
        'currentDate' => ['except' => ''],
        'viewType' => ['except' => 'week'],
    ];

    public function mount(): void
    {
        $this->currentDate = $this->currentDate ?? now()->toDateString();
        $this->selectedDate = $this->currentDate;
        $this->generateTimeSlots();
    }

    public function generateTimeSlots(): void
    {
        $this->timeSlots = [];
        $start = Carbon::parse('09:00');
        $end = Carbon::parse('21:00');

        while ($start->lt($end)) {
            $this->timeSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
    }

    public function getWeekDates(): array
    {
        $current = Carbon::parse($this->currentDate);
        $startOfWeek = $current->copy()->startOfWeek();
        $dates = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dates[] = [
                'date' => $date->toDateString(),
                'day' => $date->format('d'),
                'month' => $date->format('m'),
                'weekday' => $date->translatedFormat('D'),
                'isToday' => $date->isToday(),
                'isSelected' => $date->toDateString() === $this->selectedDate,
            ];
        }

        return $dates;
    }

    public function getBookingsForDate(string $date): array
    {
        $bookings = Booking::with('service')
            ->whereDate('appointment_time', $date)
            ->orderBy('appointment_time')
            ->get();

        $grouped = [];

        foreach ($bookings as $booking) {
            $time = $booking->appointment_time->format('H:i');
            if (!isset($grouped[$time])) {
                $grouped[$time] = [];
            }
            $grouped[$time][] = $booking;
        }

        return $grouped;
    }

    public function getBookingsForDay(string $date): \Illuminate\Support\Collection
    {
        return Booking::with('service')
            ->whereDate('appointment_time', $date)
            ->orderBy('appointment_time')
            ->get();
    }

    public function getStatistics(): array
    {
        $current = Carbon::parse($this->currentDate);

        if ($this->viewType === 'week') {
            $start = $current->copy()->startOfWeek();
            $end = $current->copy()->endOfWeek();
        } else {
            $start = $current->copy()->startOfDay();
            $end = $current->copy()->endOfDay();
        }

        $bookings = Booking::whereBetween('appointment_time', [$start, $end])->get();

        return [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
        ];
    }

    public function changeDate(string $date): void
    {
        $this->selectedDate = $date;
    }

    public function prevPeriod(): void
    {
        $current = Carbon::parse($this->currentDate);
        
        if ($this->viewType === 'week') {
            $this->currentDate = $current->subWeek()->toDateString();
        } elseif ($this->viewType === 'month') {
            $this->currentDate = $current->subMonth()->toDateString();
        } else {
            $this->currentDate = $current->subDay()->toDateString();
        }
    }

    public function nextPeriod(): void
    {
        $current = Carbon::parse($this->currentDate);
        
        if ($this->viewType === 'week') {
            $this->currentDate = $current->addWeek()->toDateString();
        } elseif ($this->viewType === 'month') {
            $this->currentDate = $current->addMonth()->toDateString();
        } else {
            $this->currentDate = $current->addDay()->toDateString();
        }
    }

    public function goToToday(): void
    {
        $this->currentDate = now()->toDateString();
        $this->selectedDate = $this->currentDate;
    }

    public function setViewType(string $type): void
    {
        $this->viewType = $type;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('list')
                ->label('Таблица')
                ->url(BookingResource::getUrl('index'))
                ->icon('heroicon-o-table-cells')
                ->color('gray'),

            Action::make('create')
                ->label('Новое бронирование')
                ->url(BookingResource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Action::make('today')
                ->label('Сегодня')
                ->action('goToToday')
                ->icon('heroicon-o-calendar')
                ->color('gray'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Календарь';
    }
}
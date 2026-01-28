<?php

namespace App\Filament\Widgets;

use App\Domains\Bookings\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        return [
            Stat::make('Бронирований сегодня', Booking::whereDate('created_at', $today)->count())
                ->description('За сегодняшний день')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Бронирований на этой неделе', Booking::where('created_at', '>=', $weekStart)->count())
                ->description('С начала недели')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Бронирований в этом месяце', Booking::where('created_at', '>=', $monthStart)->count())
                ->description('С начала месяца')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('warning'),

            Stat::make('Ожидают подтверждения', Booking::where('status', 'pending')->count())
                ->description('Требуют внимания')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }
}
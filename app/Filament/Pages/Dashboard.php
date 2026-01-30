<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\CalendarWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            BookingStats::class,
            LatestReviews::class,
            PopularServices::class,
            FilamentInfoWidget::class,
        ];
    }
}

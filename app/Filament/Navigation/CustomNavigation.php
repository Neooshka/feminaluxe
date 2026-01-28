<?php

namespace App\Filament\Navigation;

use App\Filament\Resources\BookingResource;
use Filament\Navigation\NavigationItem;

class CustomNavigation
{
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Бронирования')
                ->icon('heroicon-o-table-cells')
                ->group('Записи')
                ->sort(1)
                ->url(BookingResource::getUrl('index')),

            NavigationItem::make('Календарь')
                ->icon('heroicon-o-calendar')
                ->group('Записи')
                ->sort(2)
                ->url(BookingResource::getUrl('calendar')),
        ];
    }
}
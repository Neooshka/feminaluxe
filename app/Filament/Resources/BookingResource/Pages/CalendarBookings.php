<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Domains\Bookings\Models\Booking;
use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarBookings extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.resources.booking-resource.pages.calendar-bookings';
    protected ?string $heading = 'Календарь бронирований';
    protected ?string $subheading = 'Просмотр и управление записями в календарном виде';
    protected static ?string $title = 'Календарь';
    protected function getHeaderActions(): array
    {
        return [
            Action::make('list')
                ->label('Таблица')
                ->url(BookingResource::getUrl('index'))
                ->icon('heroicon-o-table-cells'),

            Action::make('create')
                ->label('Новое бронирование')
                ->url(BookingResource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Календарь';
    }
}

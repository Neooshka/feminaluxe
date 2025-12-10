<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Новая запись')
                ->icon('heroicon-o-plus'),  // Custom label for UX
        ];
    }

    // Optional: Add tabs or custom widgets for dashboard-like view
    // protected function getHeaderWidgets(): array { return [/* ... */]; }
}

<?php

namespace App\Filament\Widgets;

use App\Domains\Services\Models\Service;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularServices extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Популярные услуги';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Service::query()
                    ->active()
                    ->popular()
                    ->orderBy('views_count', 'desc')
                    ->limit(5)
            )
            ->paginated(false)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image_url')
                    ->label('Фото')
                    ->imageSize(40)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Категория'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB'),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Просмотры')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Бронирования')
                    ->counts('bookings')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Популярная')
                    ->boolean(),
            ]);
    }
}
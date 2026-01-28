<?php

namespace App\Filament\Widgets;

use App\Domains\Reviews\Models\Review;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Columns\RatingColumn;

class LatestReviews extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Последние отзывы';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Review::query()
                    ->with('service')
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('author')
                    ->label('Автор')
                    ->searchable()
                    ->tooltip(fn ($record): string => $record->content),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Услуга')
                    ->limit(20)
                    ->tooltip(fn ($record): string => mb_strlen($record->service->name) > 20 ? $record->service->name : ''),
                
                RatingColumn::make('rating')
                    ->label('Оценка'),

                Tables\Columns\IconColumn::make('approved')
                    ->label('Одобрено')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update(['approved' => true]))
                    ->visible(fn (Review $record) => !$record->approved),
            ]);
    }
}
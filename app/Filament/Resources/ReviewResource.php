<?php

namespace App\Filament\Resources;

use App\Domains\Reviews\Models\Review;
use App\Filament\Resources\ReviewResource\Pages;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Components\Rating;
use Mokhosh\FilamentRating\Columns\RatingColumn;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|null|\UnitEnum $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 3;

    public static function form(Form|Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Информация об авторе')
                    ->schema([
                        Forms\Components\TextInput::make('author')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(50)
                            ->columnSpan(2),
                    ])->columns(3),

                Section::make('Содержание отзыва')
                    ->schema([
                        Rating::make('rating')
                            ->label('Оценка')
                            ->required()
                            ->stars(5),

                        Forms\Components\Toggle::make('approved')
                            ->label('Одобрено')
                            ->default(false)
                            ->helperText('Отображать на сайте'),
                        
                        Forms\Components\Select::make('service_id')
                            ->label('Услуга')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('content')
                            ->label('Текст отзыва')
                            ->required()
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Техническая информация')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP-адрес')
                            ->maxLength(20)
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(2)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author')
                    ->label('Автор')
                    ->tooltip(fn ($record): string => $record->content)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Услуга')
                    ->limit(20)
                    ->tooltip(fn ($record): string => mb_strlen($record->service->name) > 20 ? $record->service->name : '')
                    ->sortable(),
                
                RatingColumn::make('rating')
                    ->label('Оценка')
                    ->sortable(),

                Tables\Columns\IconColumn::make('approved')
                    ->label('Одобрено')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('approved')
                    ->label('Статус модерации'),

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Оценка')
                    ->options([
                        1 => '★',
                        2 => '★★',
                        3 => '★★★',
                        4 => '★★★★',
                        5 => '★★★★★',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Дата отзыва')
                    ->schema([
                        Forms\Components\DatePicker::make('from')
                            ->label('С'),
                        Forms\Components\DatePicker::make('until')
                            ->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update(['approved' => true]))
                    ->visible(fn (Review $record) => !$record->approved),

                Action::make('reject')
                    ->label('Отклонить')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (Review $record) => $record->update(['approved' => false]))
                    ->visible(fn (Review $record) => $record->approved),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('approve')
                        ->label('Одобрить выбранные')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['approved' => true])),
                    BulkAction::make('reject')
                        ->label('Отклонить выбранные')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['approved' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Отзывы';
    }

    public static function getModelLabel(): string
    {
        return 'отзыв';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Отзывы';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('service');
    }
}
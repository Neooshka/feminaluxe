<?php

namespace App\Filament\Resources;

use App\Domains\Bookings\Models\Booking;
use App\Filament\Resources\BookingResource\Pages;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-calendar';

    protected static string|null|\UnitEnum $navigationGroup = 'Записи';

    protected static ?int $navigationSort = 1;

    public static function form(Form|Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Информация о клиенте')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('client_phone')
                            ->label('Телефон')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('client_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(50)
                            ->columnSpan(2),
                    ])->columns(3),

                Section::make('Детали записи')
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->label('Услуга')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DateTimePicker::make('appointment_time')
                            ->label('Дата и время')
                            ->required()
                            ->seconds(false)
                            ->minutesStep(30)
                            ->weekStartsOnMonday()
                            ->displayFormat('d.m.Y H:i'),

                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'pending' => 'Ожидание',
                                'confirmed' => 'Подтверждено',
                                'cancelled' => 'Отменено',
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\Select::make('source')
                            ->label('Источник')
                            ->options([
                                'website' => 'Сайт',
                                'phone' => 'Телефон',
                                'instagram' => 'Instagram',
                                'vk' => 'VK',
                                'other' => 'Другое',
                            ])
                            ->default('website'),
                    ])->columns(2),

                Section::make('UTM-метки')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('utm_source')
                            ->label('Источник кампании'),

                        Forms\Components\TextInput::make('utm_medium')
                            ->label('Тип трафика'),

                        Forms\Components\TextInput::make('utm_campaign')
                            ->label('Название кампании'),

                        Forms\Components\TextInput::make('utm_content')
                            ->label('Контент'),

                        Forms\Components\TextInput::make('utm_term')
                            ->label('Ключевое слово'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client_phone')
                    ->label('Телефон')
                    ->searchable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Услуга')
                    ->sortable()
                    ->limit(20)
                    ->tooltip(fn ($record): string => mb_strlen($record->service->name) > 20 ? $record->service->name : ''),

                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                    ])
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Ожидание',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидание',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                    ]),

                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('appointment_time')
                    ->label('Дата записи')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_time', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_time', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Подтвердить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Booking $record) => $record->confirm())
                    ->visible(fn (Booking $record) => $record->status === 'pending'),

               Action::make('cancel')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Booking $record) => $record->cancel())
                    ->visible(fn (Booking $record) => $record->status !== 'cancelled'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('confirm')
                        ->label('Подтвердить выбранные')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->confirm()),
                    BulkAction::make('cancel')
                        ->label('Отменить выбранные')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->cancel()),
                ]),
            ])
            ->defaultSort('appointment_time', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'calendar' => Pages\CalendarBookings::route('/calendar'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Бронирования';
    }

    public static function getModelLabel(): string
    {
        return 'бронирование';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Бронирования';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('service');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationItems(): array
    {
        return [
            \Filament\Navigation\NavigationItem::make()
                ->label(static::getNavigationLabel())
                ->icon(static::getNavigationIcon())
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort())
                ->url(static::getUrl('index')),

            \Filament\Navigation\NavigationItem::make()
                ->label('Календарь')
                ->icon('heroicon-o-calendar')
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort() + 1)
                ->url(static::getUrl('calendar')),
        ];
    }
}
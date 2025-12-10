<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Bookings\Models\Booking;
use App\Filament\Resources\BookingResource\Pages\CreateBooking;
use App\Filament\Resources\BookingResource\Pages\EditBooking;
use App\Filament\Resources\BookingResource\Pages\ListBookings;
use App\Filament\Resources\BookingResource\Pages\ViewBooking;
use BackedEnum;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-calendar-days';  // Modern icon for 2025 UX

    protected static ?string $label = 'Запись';

    protected static ?string $pluralLabel = 'Записи';

    protected static string|null|UnitEnum $navigationGroup = 'Управление';  // Grouping for sidebar scalability

    // Authorization: Uncomment and create Policy if needed
    // protected static ?string $recordTitleAttribute = 'client_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('service_id')
                    ->label('Услуга')
                    ->relationship(name: 'service', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('client_name')
                    ->label('Имя клиента')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Введите имя'),
                TextInput::make('client_email')
                    ->label('Email клиента')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('example@email.com'),
                TextInput::make('client_phone')
                    ->label('Телефон клиента')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->placeholder('+7 (XXX) XXX-XX-XX'),
                DateTimePicker::make('appointment_time')
                    ->label('Дата и время записи')
                    ->required()
                    ->minDate(now())
                    ->displayFormat('d.m.Y H:i')
                    ->native(false),
                Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает подтверждения',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                    ])
                    ->default('pending')
                    ->required(),
            ])
            ->columns();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('client_name')
            ->columns([
                TextColumn::make('service.name')
                    ->label('Услуга')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('client_name')
                    ->label('Имя клиента')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('client_email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                TextColumn::make('client_phone')
                    ->label('Телефон')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                TextColumn::make('appointment_time')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтверждено',
                        'cancelled' => 'Отменено',
                        default => $state,
                    }),
            ])
            ->filters([
                // Example: SelectFilter::make('status')->options([...])
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Просмотр'),
                    EditAction::make()->label('Редактировать'),
                    Action::make('confirm')
                        ->label('Подтвердить')
                        ->color('success')
                        ->action(fn (Booking $record) => $record->confirm())
                        ->requiresConfirmation()
                        ->visible(fn (Booking $record): bool => $record->status === 'pending'),
                    Action::make('cancel')
                        ->label('Отменить')
                        ->color('danger')
                        ->action(fn (Booking $record) => $record->cancel())
                        ->requiresConfirmation()
                        ->visible(fn (Booking $record): bool => $record->status !== 'cancelled'),
                    DeleteAction::make()->label('Удалить'),
                    RestoreAction::make()->label('Восстановить')->visible(fn (Booking $record): bool => $record->trashed()),
                ]),
            ])
            ->bulkActions([
                // Bulk confirm: Actions\BulkAction::make('confirmAll')->action(fn (Collection $records) => $records->each->confirm())
            ])
            ->poll('60s')
            ->defaultSort('appointment_time', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('service')
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'view' => ViewBooking::route('/{record}'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}

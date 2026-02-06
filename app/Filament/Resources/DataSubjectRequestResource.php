<?php
namespace App\Filament\Resources;

use App\Models\DataSubjectRequest;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Forms;

class DataSubjectRequestResource extends Resource
{
    protected static ?string $model = DataSubjectRequest::class;
    
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('subject_name')
                    ->label('ФИО субъекта')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Телефон')
                    ->tel()
                    ->required(),
                Forms\Components\Select::make('request_type')
                    ->label('Тип запроса')
                    ->options([
                        'access' => 'Доступ к данным',
                        'correction' => 'Исправление данных',
                        'deletion' => 'Удаление данных',
                        'withdrawal' => 'Отзыв согласия',
                        'complaint' => 'Жалоба',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->label('Содержание запроса')
                    ->required()
                    ->rows(5),
                Forms\Components\DatePicker::make('received_at')
                    ->label('Дата получения')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('deadline')
                    ->label('Срок ответа до')
                    ->default(now()->addDays(30))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'received' => 'Получен',
                        'in_progress' => 'В обработке',
                        'completed' => 'Выполнен',
                        'rejected' => 'Отклонен',
                    ])
                    ->default('received')
                    ->required(),
            ]);
    }
    
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('received_at')
                    ->label('Получен')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_name')
                    ->label('ФИО')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'primary' => 'received',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Срок до')
                    ->date('d.m.Y')
                    ->color(fn ($record) => 
                        $record->deadline < now() ? 'danger' : 'default'
                    ),
            ]);
    }
}
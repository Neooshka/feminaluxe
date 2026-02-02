<?php

namespace App\Filament\Resources;

use App\Domains\Services\Models\Service;
use App\Filament\Resources\ServiceResource\Pages;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-sparkles';

    protected static string|null|\UnitEnum $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 1;

    public static function form(Form|Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()->schema([
                    Section::make('Основная информация')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Название')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('slug', Str::slug($state));
                                })
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('category')
                                ->label('Категория')
                                ->required()
                                ->maxLength(100)
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('slug')
                                ->label('URL-адрес')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Уникальный идентификатор для URL')
                                ->columnSpanFull(),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Активна')
                                ->default(true)
                                ->columnSpan(3),

                            Forms\Components\Toggle::make('is_popular')
                                ->label('Популярная услуга')
                                ->default(false)
                                ->columnSpan(2),
                        ])->columns(5),

                    Section::make('Цены и длительность')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->label('Цена')
                                ->numeric()
                                ->required()
                                ->prefix('₽')
                                ->step(0.01),

                            Forms\Components\TextInput::make('discounted_price')
                                ->label('Цена со скидкой')
                                ->numeric()
                                ->prefix('₽')
                                ->step(0.01)
                                ->helperText('Оставьте пустым, если скидки нет'),

                            Forms\Components\TextInput::make('duration')
                                ->label('Длительность')
                                ->maxLength(50)
                                ->helperText('Например: "2-3 часа"'),

                            Forms\Components\Toggle::make('has_promotion')
                                ->label('Акция')
                                ->reactive()
                                ->helperText('Включить отображение акции'),

                            Forms\Components\DateTimePicker::make('featured_until')
                                ->label('Акция действует до')
                                ->hidden(fn ($get) => !$get('has_promotion')),
                        ])->columns(2),

                    Section::make('Изображения')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('before_after')
                                ->label('Фото до/после')
                                ->disk('media')
                                ->collection('before_after')
                                ->image()
                                ->directory('services/before-after')
                                ->maxSize(10240)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Основное фото услуги')
                                ->conversion('thumb')
                                ->responsiveImages()
                                ->multiple(false)
                                ->reorderable(true),

                            SpatieMediaLibraryFileUpload::make('gallery')
                                ->label('Галерея')
                                ->disk('media')
                                ->collection('gallery')
                                ->image()
                                ->directory('services/gallery')
                                ->multiple()
                                ->maxFiles(10)
                                ->reorderable()
                                ->maxSize(10240)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Дополнительные фото услуги')
                                ->responsiveImages()
                                ->conversion('thumb'),
                        ]),
                ])->columns(1),
                Group::make()->schema([
                    Section::make('Описания')
                        ->schema([
                            Forms\Components\Textarea::make('short_description')
                                ->label('Краткое описание')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Для карточек и превью'),

                            Forms\Components\RichEditor::make('description')
                                ->label('Описание')
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('detailed_description')
                                ->label('Детальное описание')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Дополнительно')
                        ->schema([
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Порядок сортировки')
                                ->numeric()
                                ->default(0),

                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Дата публикации')
                                ->default(now()),

                            Forms\Components\KeyValue::make('benefits')
                                ->label('Преимущества')
                                ->keyLabel('Заголовок')
                                ->valueLabel('Описание'),

                            Forms\Components\KeyValue::make('contraindications')
                                ->label('Противопоказания'),
                        ])->columns(2),
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image_url')
                    ->label('Фото')
                    ->imageSize(50)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Категория')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Популярная')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Категория')
                    ->options(fn () => Service::query()->pluck('category', 'category')->unique()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность'),

                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label('Популярные'),

                Tables\Filters\TernaryFilter::make('has_promotion')
                    ->label('Со скидкой'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['bookings', 'reviews']);
    }

    public static function getNavigationLabel(): string
    {
        return 'Услуги';
    }

    public static function getModelLabel(): string
    {
        return 'услугу';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Услуги';
    }
}
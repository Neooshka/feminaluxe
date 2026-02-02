<?php

namespace App\Filament\Resources;

use App\Domains\Portfolio\Models\Portfolio;
use App\Filament\Resources\PortfolioResource\Pages;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PortfolioResource extends Resource
{
    protected static ?string $model = Portfolio::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-photo';

    protected static string|null|\UnitEnum $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 5;

    public static function form(Form|Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()->schema([
                    Section::make('Основная информация')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Название работы')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('slug', Str::slug($state));
                                }),
    
                            Forms\Components\TextInput::make('slug')
                                ->label('URL-адрес')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Уникальный идентификатор для URL'),
    
                            Forms\Components\Select::make('service_id')
                                ->label('Услуга')
                                ->relationship('service', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
    
                            Forms\Components\Textarea::make('description')
                                ->label('Описание')
                                ->rows(3)
                                ->maxLength(1000),
    
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Порядок сортировки')
                                ->numeric()
                                ->default(0),
    
                            Forms\Components\Toggle::make('is_published')
                                ->label('Опубликовано')
                                ->default(false),
    
                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Дата публикации')
                                ->default(now()),
                        ])->columns(2),
    
                    Section::make('SEO')
                        ->schema([
                            Forms\Components\Textarea::make('seo.description')
                                ->label('Meta Description')
                                ->rows(3)
                                ->maxLength(160),
    
                            Forms\Components\TextInput::make('seo.title')
                                ->label('Meta Title')
                                ->maxLength(60),
                        ]),
                ])->columns(1),
                Group::make()->schema([
                    Section::make('Фотографии')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('before')
                                ->label('Фото "До"')
                                ->disk('media')
                                ->collection('before')
                                ->image()
                                ->directory('portfolio/before')
                                ->maxSize(10240)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Фото до процедуры'),
    
                            SpatieMediaLibraryFileUpload::make('after')
                                ->label('Фото "После"')
                                ->disk('media')
                                ->collection('after')
                                ->image()
                                ->directory('portfolio/after')
                                ->maxSize(10240)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Фото после процедуры'),
    
                            SpatieMediaLibraryFileUpload::make('gallery')
                                ->label('Дополнительные фото')
                                ->disk('media')
                                ->collection('gallery')
                                ->image()
                                ->directory('portfolio/gallery')
                                ->multiple()
                                ->reorderable()
                                ->maxSize(10240)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Дополнительные фотографии работы'),
                        ]),
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Услуга')
                    ->limit(20)
                    ->tooltip(fn ($record): string => mb_strlen($record->service->name) > 20 ? $record->service->name : '')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Дата публикации')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Услуга')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Статус публикации'),

                Tables\Filters\Filter::make('published_at')
                    ->label('Дата публикации')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Посмотреть')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Portfolio $record) => route('portfolio', [$record]))
                    ->openUrlInNewTab()
                    ->visible(fn (Portfolio $record) => $record->is_published),

                Action::make('publish')
                    ->label('Опубликовать')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Portfolio $record) => $record->update([
                        'is_published' => true,
                        'published_at' => now(),
                    ]))
                    ->visible(fn (Portfolio $record) => !$record->is_published),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('publish')
                        ->label('Опубликовать')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update([
                            'is_published' => true,
                            'published_at' => now(),
                        ])),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Портфолио';
    }

    public static function getModelLabel(): string
    {
        return 'работу';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Портфолио';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('service');
    }
}
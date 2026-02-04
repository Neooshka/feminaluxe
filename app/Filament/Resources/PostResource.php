<?php

namespace App\Filament\Resources;

use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Models\Tag;
use App\Filament\Resources\PostResource\Pages;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-newspaper';

    protected static string|null|\UnitEnum $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 2;

    public static function form(Form|Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL-адрес')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->helperText('Уникальный идентификатор для URL'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Дата публикации')
                            ->default(now())
                            ->helperText('Запланировать публикацию на будущее'),
                    ])->columns(2),

                Section::make('Содержание')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Текст статьи')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog')
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('featured')
                            ->label('Обложка статьи')
                            ->collection('featured')
                            ->image()
                            ->directory('blog/featured')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Рекомендуемый размер: 1200x630px'),
                    ]),

                Section::make('Теги')
                    ->schema([
                        Forms\Components\Select::make('tags')
                            ->label('Теги')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Название')
                                    ->required(),
                            ])
                            ->createOptionUsing(function ($data) {
                                $data['slug'] = Str::slug($data['name']);
                                return Tag::create($data)->id;
                            }),
                    ]),

                Section::make('SEO')
                    ->schema([
                        Forms\Components\Textarea::make('seo.description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Краткое описание для поисковых систем'),

                        Forms\Components\TextInput::make('seo.title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->helperText('Заголовок для SEO (по умолчанию используется заголовок статьи)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured')
                    ->label('Обложка')
                    ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('featured'))
                    ->imageSize(50)
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Дата публикации')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn ($state) => $state?->isFuture() ? 'warning' : null)
                    ->tooltip(fn ($state) => $state?->isFuture() ? 'Запланировано' : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('published_at')
                    ->label('Дата публикации')
                    ->form([
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

                Tables\Filters\TernaryFilter::make('published')
                    ->label('Опубликованные')
                    ->queries(
                        true: fn (Builder $query) => $query->published(),
                        false: fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '>', now()),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Посмотреть')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Post $record) => route('blog.show', $record))
                    ->openUrlInNewTab(),

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
                        ->action(fn ($records) => $records->each->update(['published_at' => now()])),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Блог';
    }

    public static function getModelLabel(): string
    {
        return 'статью';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Статьи блога';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
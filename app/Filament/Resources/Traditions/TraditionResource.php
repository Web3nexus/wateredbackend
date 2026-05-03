<?php

namespace App\Filament\Resources\Traditions;

use App\Filament\Resources\Traditions\Pages;
use App\Models\Tradition;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;
use BackedEnum;
use UnitEnum;

class TraditionResource extends Resource
{
    protected static ?string $model = Tradition::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-flag';

    protected static UnitEnum|string|null $navigationGroup = 'Library';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(Tradition::class, 'slug', ignoreRecord: true),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(999)
                            ->required(),

                        RichEditor::make('description.en')
                            ->label('Description (English)')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Attributes')
                    ->schema([
                        Select::make('language_id')
                            ->relationship('language', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('tradition_type')
                            ->placeholder('e.g. scripture, history, deity, orders')
                            ->default('scripture')
                            ->required(),

                        TextInput::make('african_origin')
                            ->placeholder('e.g. igbo, yoruba, nigeria')
                            ->nullable(),

                        Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Section::make('Visuals')
                    ->schema([
                        FileUpload::make('deity_image_url')
                            ->label('Banner/Deity Image')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->disk('public')
                            ->directory('traditions'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label('#'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tradition_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('african_origin')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTraditions::route('/'),
            'create' => Pages\CreateTradition::route('/create'),
            'edit' => Pages\EditTradition::route('/{record}/edit'),
        ];
    }
}

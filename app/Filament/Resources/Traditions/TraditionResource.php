<?php

namespace App\Filament\Resources\Traditions;

use App\Filament\Resources\Traditions\Pages;
use App\Models\Tradition;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(Tradition::class, 'slug', ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Attributes')
                    ->schema([
                        Forms\Components\Select::make('language_id')
                            ->relationship('language', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('tradition_type')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('african_origin')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Visuals')
                    ->schema([
                        Forms\Components\FileUpload::make('deity_image_url')
                            ->label('Banner/Deity Image')
                            ->image()
                            ->directory('traditions'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

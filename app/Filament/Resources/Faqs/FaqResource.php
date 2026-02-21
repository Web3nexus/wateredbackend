<?php

namespace App\Filament\Resources\Faqs;

use App\Models\Faq;
use Filament\Resources\Resource;
use App\Filament\Resources\Faqs\Pages;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static \UnitEnum|string|null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 5;
    protected static \UnitEnum|string|null $navigationLabel = 'FAQs';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('FAQ Details')->schema([
                \Filament\Forms\Components\TextInput::make('category')
                    ->maxLength(255)
                    ->default('General'),
                \Filament\Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),
                \Filament\Forms\Components\Textarea::make('question')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
                \Filament\Forms\Components\Textarea::make('answer')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ])->columns(2),
        ]);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(60)
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('category')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->filters([])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}

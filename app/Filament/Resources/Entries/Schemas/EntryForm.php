<?php

namespace App\Filament\Resources\Entries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class EntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chapter_id')
                    ->relationship('chapter', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('number')
                    ->required()
                    ->numeric()
                    ->label('Verse Number'),
                RichEditor::make('text')
                    ->required()
                    ->columnSpanFull()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('entries/images')
                    ->fileAttachmentsVisibility('public')
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->placeholder('Enter the sacred text here... You can add images and formatting.'),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}

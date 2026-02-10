<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabled(fn($record) => $record !== null)
                    ->unique(ignoreRecord: true),
                TextInput::make('subject')
                    ->required(),
                RichEditor::make('body')
                    ->required()
                    ->columnSpanFull()
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
                        'undo',
                    ]),
                TextInput::make('description')
                    ->columnSpanFull(),
                Placeholder::make('placeholders_hint')
                    ->label('Available Placeholders')
                    ->content(fn($get) => match ($get('key')) {
                        'verification' => '{{ name }}, {{ verification_url }}, {{ app_name }}',
                        'welcome' => '{{ name }}, {{ app_name }}',
                        'newsletter' => '{{ name }}, {{ app_name }}, {{ unsubscribe_url }}',
                        default => '{{ name }}, {{ app_name }}',
                    })
                    ->visible(fn($get) => !empty($get('key'))),
            ]);
    }
}

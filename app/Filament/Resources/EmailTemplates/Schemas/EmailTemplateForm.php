<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('description'),
            ]);
    }
}

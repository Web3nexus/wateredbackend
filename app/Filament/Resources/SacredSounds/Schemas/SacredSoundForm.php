<?php

namespace App\Filament\Resources\SacredSounds\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SacredSoundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->placeholder('e.g. Deep Meditation'),

                Select::make('type')
                    ->options([
                        'meditation' => 'Meditation',
                        'drum' => 'Drum',
                        'bell' => 'Bell',
                        'chant' => 'Chant',
                        'nature' => 'Nature',
                        'instrumental' => 'Instrumental',
                    ])
                    ->required(),

                FileUpload::make('file_path')
                    ->label('Audio File')
                    ->disk('public')
                    ->directory('sacred_sounds')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/x-m4a', 'audio/mp4'])
                    ->maxSize(51200) // 50MB
                    ->required(),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
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
                FileUpload::make('image_url')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->disk('public')
                    ->directory('announcements')
                    ->image(),
                TextInput::make('link_url')
                    ->label('Hyperlink (External or Deep Link)')
                    ->url()
                    ->placeholder('https://...'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled Publish Date (Optional)'),
                Toggle::make('is_urgent')
                    ->label('Mark as Urgent')
                    ->default(false),
                Toggle::make('push_enabled')
                    ->label('Send Push Notification')
                    ->default(true),
                Select::make('target_audience')
                    ->options([
                        'all' => 'All Users',
                        'premium' => 'Premium Users Only',
                    ])
                    ->default('all')
                    ->required(),
            ]);
    }
}

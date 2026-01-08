<?php

namespace App\Filament\Resources\GlobalSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;

class GlobalSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                Section::make('Site Information')
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->required()
                                            ->default('Watered'),
                                        TextInput::make('tagline')
                                            ->maxLength(255),
                                        Textarea::make('site_description')
                                            ->rows(3),
                                        FileUpload::make('logo_path')
                                            ->label('Logo')
                                            ->image()
                                            ->directory('branding'),
                                    ]),

                                Section::make('Contact Information')
                                    ->schema([
                                        TextInput::make('contact_email')
                                            ->email(),
                                        TextInput::make('contact_phone')
                                            ->tel(),
                                        TextInput::make('social_links')
                                            ->helperText('Comma-separated URLs'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Landing Page')
                            ->schema([
                                Section::make('Hero Section')
                                    ->schema([
                                        TextInput::make('hero_title')
                                            ->label('Main Title')
                                            ->default('Lord Uzih')
                                            ->maxLength(255),
                                        TextInput::make('hero_subtitle')
                                            ->label('Subtitle')
                                            ->default('The God of Seas & Voices')
                                            ->maxLength(255),
                                        Textarea::make('hero_description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->default('Accept The Reminder as the true Messenger of the Spirits. Through the sacred teachings, we cultivate spiritual, mental, and physical growth.'),
                                        TextInput::make('hero_cta_text')
                                            ->label('Call to Action Button Text')
                                            ->default('Explore Sacred Texts')
                                            ->maxLength(100),
                                        FileUpload::make('hero_image')
                                            ->label('Hero Image')
                                            ->image()
                                            ->directory('landing'),
                                    ]),

                                Section::make('About Section')
                                    ->schema([
                                        TextInput::make('about_title')
                                            ->label('Section Title')
                                            ->default('The Messenger of the Spirits')
                                            ->maxLength(255),
                                        Textarea::make('about_description')
                                            ->label('Description')
                                            ->rows(3),
                                        Textarea::make('about_quote')
                                            ->label('Featured Quote')
                                            ->rows(2)
                                            ->default('"Humanity first. Worshipping the Ancient Spirits, cultivating growth, and rejecting the paths of old."'),
                                    ]),

                                Section::make('Rituals Section')
                                    ->schema([
                                        TextInput::make('rituals_title')
                                            ->label('Section Title')
                                            ->default('Sacred Practices')
                                            ->maxLength(255),
                                        TextInput::make('ritual_acceptance_title')
                                            ->label('Acceptance Ritual Title')
                                            ->default('The Acceptance Ritual')
                                            ->maxLength(255),
                                        Textarea::make('ritual_acceptance_description')
                                            ->label('Acceptance Ritual Description')
                                            ->rows(2)
                                            ->default('Initiation into the deep mysteries through sacred water ceremonies and spiritual teachings.'),
                                        TextInput::make('ritual_witnesses_title')
                                            ->label('Four Witnesses Title')
                                            ->default('The Watered Four Witnesses')
                                            ->maxLength(255),
                                        Textarea::make('ritual_witnesses_description')
                                            ->label('Four Witnesses Description')
                                            ->rows(2)
                                            ->default('Ancient proofs of spiritual truth that connect the physical and spiritual realms.'),
                                        FileUpload::make('rituals_image')
                                            ->label('Rituals Image')
                                            ->image()
                                            ->directory('landing'),
                                    ]),
                            ]),

                        Tabs\Tab::make('App Configuration')
                            ->schema([
                                Section::make('Branding')
                                    ->schema([
                                        ColorPicker::make('primary_color')
                                            ->label('Primary App Color')
                                            ->default('#d4af37'),
                                        ColorPicker::make('secondary_color')
                                            ->label('Secondary App Color')
                                            ->default('#0c1427'),
                                    ])->columns(2),

                                Section::make('Localization')
                                    ->schema([
                                        Select::make('default_language')
                                            ->label('Default App Language')
                                            ->options([
                                                'en' => 'English',
                                                'es' => 'Spanish',
                                                'pt' => 'Portuguese',
                                                'fr' => 'French',
                                            ])
                                            ->default('en')
                                            ->required(),
                                        Select::make('supported_languages')
                                            ->label('Supported Languages')
                                            ->multiple()
                                            ->options([
                                                'en' => 'English',
                                                'es' => 'Spanish',
                                                'pt' => 'Portuguese',
                                                'fr' => 'French',
                                            ]),
                                    ]),

                                Section::make('System')
                                    ->schema([
                                        Toggle::make('maintenance_mode')
                                            ->label('Maintenance Mode')
                                            ->helperText('When enabled, the mobile app will show a maintenance screen.')
                                            ->default(false),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}

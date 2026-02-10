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
use Filament\Forms\Components\RichEditor;

class GlobalSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->columnSpanFull()
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
                                        FileUpload::make('favicon_path')
                                            ->label('Favicon')
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
                                Section::make('Configuration')
                                    ->schema([
                                        Toggle::make('is_landing_page_enabled')
                                            ->label('Enable Landing Page')
                                            ->helperText('If disabled, visitors will see a coming soon page or be redirected.')
                                            ->default(true),
                                    ]),
                                Section::make('App Download Links')
                                    ->schema([
                                        TextInput::make('android_download_url')
                                            ->label('Android Download URL (Play Store)')
                                            ->url()
                                            ->placeholder('https://play.google.com/store/apps/details?id=...'),
                                        TextInput::make('ios_download_url')
                                            ->label('iOS Download URL (App Store)')
                                            ->url()
                                            ->placeholder('https://apps.apple.com/app/...'),
                                    ]),
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
                                            ->default('"Humanity first. Worshipping The Gods, cultivating growth, and rejecting the paths of old."'),
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

                        Tabs\Tab::make('Monetization & Ads')
                            ->schema([
                                Section::make('Currency Settings')
                                    ->schema([
                                        Select::make('system_currency')
                                            ->label('System Currency')
                                            ->options([
                                                'USD' => 'USD ($)',
                                                'NGN' => 'NGN (₦)',
                                            ])
                                            ->default('USD')
                                            ->required(),
                                    ]),

                                Section::make('Google AdMob')
                                    ->schema([
                                        Toggle::make('is_ads_enabled')
                                            ->label('Enable Ads')
                                            ->helperText('Logic: Only free mode has ads.')
                                            ->default(false),
                                        TextInput::make('ad_unit_id_android')
                                            ->label('Android Ad Unit ID'),
                                        TextInput::make('ad_unit_id_ios')
                                            ->label('iOS Ad Unit ID'),
                                        Select::make('ads_screens')
                                            ->label('Screens to show Ads')
                                            ->multiple()
                                            ->options([
                                                'library' => 'Library',
                                                'audio' => 'Audio Feed',
                                                'reels' => 'Reels',
                                                'community' => 'Community',
                                                'profile' => 'Profile',
                                            ]),
                                    ]),

                                Section::make('Stripe Payments')
                                    ->schema([
                                        TextInput::make('stripe_public_key')
                                            ->label('Stripe Publishable Key'),
                                        TextInput::make('stripe_secret_key')
                                            ->label('Stripe Secret Key')
                                            ->password()
                                            ->dehydrated(fn(?string $state) => filled($state)),
                                    ])->columns(2),

                                Section::make('Paystack Payments')
                                    ->schema([
                                        TextInput::make('paystack_public_key')
                                            ->label('Paystack Public Key'),
                                        TextInput::make('paystack_secret_key')
                                            ->label('Paystack Secret Key')
                                            ->password()
                                            ->dehydrated(fn(?string $state) => filled($state)),
                                    ])->columns(2),

                                Section::make('Flutterwave Payments')
                                    ->schema([
                                        TextInput::make('flutterwave_public_key')
                                            ->label('Flutterwave Public Key'),
                                        TextInput::make('flutterwave_secret_key')
                                            ->label('Flutterwave Secret Key')
                                            ->password()
                                            ->dehydrated(fn(?string $state) => filled($state)),
                                    ])->columns(2),

                                Section::make('In-App Purchase (IAP) Product IDs')
                                    ->schema([
                                        TextInput::make('premium_monthly_id')
                                            ->label('Monthly Subscription ID')
                                            ->placeholder('e.g. premium_monthly'),
                                        TextInput::make('premium_yearly_id')
                                            ->label('Yearly Subscription ID')
                                            ->placeholder('e.g. premium_yearly'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('App Stores')
                            ->schema([
                                Section::make('Apple App Store')
                                    ->schema([
                                        TextInput::make('apple_shared_secret')
                                            ->label('App Store Shared Secret')
                                            ->password()
                                            ->dehydrated(fn(?string $state) => filled($state)),
                                    ]),

                                Section::make('Google Play Store')
                                    ->schema([
                                        TextInput::make('google_play_package_name')
                                            ->label('Package Name')
                                            ->placeholder('com.example.app'),
                                        Textarea::make('google_play_service_account_json')
                                            ->label('Service Account JSON')
                                            ->rows(5)
                                            ->dehydrated(fn(?string $state) => filled($state)),
                                    ]),
                            ]),

                        Tabs\Tab::make('Sound & Notifications')
                            ->schema([
                                Section::make('Custom App Sounds')
                                    ->schema([
                                        FileUpload::make('notification_sound_path')
                                            ->label('Default Notification Sound')
                                            ->directory('sounds')
                                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg']),
                                        FileUpload::make('alarm_sound_path')
                                            ->label('Alarm Sound')
                                            ->directory('sounds')
                                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg']),
                                    ]),
                            ]),

                        Tabs\Tab::make('Legal Documents')
                            ->schema([
                                Section::make('Privacy Policy')
                                    ->schema([
                                        RichEditor::make('privacy_policy')
                                            ->label('Privacy Policy Content')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Terms of Service')
                                    ->schema([
                                        RichEditor::make('terms_of_service')
                                            ->label('Terms of Service Content')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Mail Settings')
                            ->schema([
                                Section::make('SMTP Configuration')
                                    ->schema([
                                        Select::make('mail_mailer')
                                            ->label('Mailer')
                                            ->options([
                                                'smtp' => 'SMTP',
                                                'log' => 'Log (Debug)',
                                            ])
                                            ->default('smtp')
                                            ->required(),
                                        TextInput::make('mail_host')
                                            ->label('SMTP Host')
                                            ->placeholder('smtp.mailtrap.io'),
                                        TextInput::make('mail_port')
                                            ->label('SMTP Port')
                                            ->placeholder('2525'),
                                        TextInput::make('mail_username')
                                            ->label('SMTP Username'),
                                        TextInput::make('mail_password')
                                            ->label('SMTP Password')
                                            ->password()
                                            ->dehydrated(fn(?string $state) => filled($state)),
                                        Select::make('mail_encryption')
                                            ->label('Encryption')
                                            ->options([
                                                'tls' => 'TLS',
                                                'ssl' => 'SSL',
                                                null => 'None',
                                            ]),
                                    ])->columns(2),
                                Section::make('Sender Information')
                                    ->schema([
                                        TextInput::make('mail_from_address')
                                            ->label('From Email Address')
                                            ->email()
                                            ->placeholder('hello@mywatered.com'),
                                        TextInput::make('mail_from_name')
                                            ->label('From Name')
                                            ->placeholder('Watered Team'),
                                    ])->columns(2),
                            ]),
                    ]),
            ]);
    }
}

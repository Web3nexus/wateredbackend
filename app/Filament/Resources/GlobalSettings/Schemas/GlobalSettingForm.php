<?php

namespace App\Filament\Resources\GlobalSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

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
                                        TextInput::make('site_name'),
                                        TextInput::make('tagline')
                                            ->maxLength(255),
                                        Textarea::make('site_description')
                                            ->rows(3),
                                        FileUpload::make('logo_path')
                                            ->label('Logo')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                            ->disk('public')
                                            ->directory('branding'),
                                        FileUpload::make('favicon_path')
                                            ->label('Favicon')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                            ->disk('public')
                                            ->directory('branding'),
                                    ]),

                                Section::make('App Colors (Branding)')
                                    ->schema([
                                        ColorPicker::make('primary_color')
                                            ->label('Primary App Color')
                                            ->default('#d4af37'),
                                        ColorPicker::make('secondary_color')
                                            ->label('Secondary App Color')
                                            ->default('#0c1427'),
                                    ])->columns(2),

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
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                            ->disk('public')
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
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                            ->disk('public')
                                            ->directory('landing'),
                                    ]),
                            ]),

                        Tabs\Tab::make('App Configuration')
                            ->schema([

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
                                            ->default('en'),
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
                                                'GBP' => 'GBP (£)',
                                                'EUR' => 'EUR (€)',
                                            ])
                                            ->default('NGN'),
                                        TextInput::make('currency_symbol')
                                            ->label('Currency Symbol')
                                            ->default('₦')
                                            ->maxLength(10)
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        Select::make('currency_position')
                                            ->label('Currency Position')
                                            ->options([
                                                'before' => 'Before Amount ($100)',
                                                'after' => 'After Amount (100$)',
                                            ])
                                            ->default('before'),
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
                                        static::addSecureRevealAction(
                                            TextInput::make('stripe_public_key')
                                                ->label('Stripe Publishable Key')
                                        ),
                                        static::addSecureRevealAction(
                                            TextInput::make('stripe_secret_key')
                                                ->label('Stripe Secret Key')
                                        ),
                                    ])->columns(2),
                                Section::make('Paystack Payments')
                                    ->schema([
                                        static::addSecureRevealAction(
                                            TextInput::make('paystack_public_key')
                                                ->label('Paystack Public Key')
                                        ),
                                        static::addSecureRevealAction(
                                            TextInput::make('paystack_secret_key')
                                                ->label('Paystack Secret Key')
                                        ),
                                    ])->columns(2),
                                Section::make('Flutterwave Payments')
                                    ->schema([
                                        static::addSecureRevealAction(
                                            TextInput::make('flutterwave_public_key')
                                                ->label('Flutterwave Public Key')
                                        ),
                                        static::addSecureRevealAction(
                                            TextInput::make('flutterwave_secret_key')
                                                ->label('Flutterwave Secret Key')
                                        ),
                                    ])->columns(2),

                                Section::make('Premium Subscription Pricing (Local/NGN)')
                                    ->schema([
                                        TextInput::make('paystack_monthly_plan_code')
                                            ->label('Paystack Monthly Plan Code')
                                            ->placeholder('e.g. PLN_gx2wn530m0i3w3m')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_monthly_id')
                                            ->label('Monthly Product ID')
                                            ->placeholder('e.g. premium_monthly')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_monthly_price')
                                            ->label('Monthly Display Price')
                                            ->placeholder('e.g. ₦5,000')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_monthly_amount')
                                            ->label('Monthly Charge Amount (Kobo)')
                                            ->numeric()
                                            ->placeholder('500000')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),

                                        TextInput::make('paystack_yearly_plan_code')
                                            ->label('Paystack Yearly Plan Code')
                                            ->placeholder('e.g. PLN_gx2wn530m0i3w3m')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_yearly_id')
                                            ->label('Yearly Product ID')
                                            ->placeholder('e.g. premium_yearly')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_yearly_price')
                                            ->label('Yearly Display Price')
                                            ->placeholder('e.g. ₦50,000')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_yearly_amount')
                                            ->label('Yearly Charge Amount (Kobo)')
                                            ->numeric()
                                            ->placeholder('5000000')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                    ])->columns(3),

                                Section::make('Premium Plan Marketing Text')
                                    ->schema([
                                        TextInput::make('premium_title')
                                            ->label('Premium Page Title')
                                            ->placeholder('e.g. WATERED PLUS+')
                                            ->default('WATERED PLUS+')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_subtitle')
                                            ->label('Premium Page Subtitle')
                                            ->placeholder('e.g. Unlock the full depth of African spirituality.')
                                            ->default('Unlock the full depth of African spirituality.')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TagsInput::make('premium_features')
                                            ->label('Plan Features')
                                            ->placeholder('Add a feature and press Enter...')
                                            ->helperText('These features are shown on the subscription plans in the app. Edit or add your own.')
                                            ->suggestions([
                                                'Complete Sacred Library',
                                                'Daily Audio Teachings',
                                                'Community Access',
                                                'Unlimited Rituals',
                                                'Everything in Monthly',
                                                '2 Months Free',
                                                'Exclusive Yearly Content',
                                                'Priority Support',
                                                'Ad-Free Experience',
                                                'Offline Access',
                                                'Nima Sedani Access',
                                                'Sacred Calendar',
                                                'Daily Wisdom Verses',
                                                'Direct Spiritual Guidance',
                                            ])
                                            ->default([
                                                'Complete Sacred Library',
                                                'Daily Audio Teachings',
                                                'Community Access',
                                                'Unlimited Rituals',
                                            ]),
                                    ])->columns(2),

                                Section::make('Premium Subscription Pricing (International/USD)')
                                    ->description('These prices will be shown to users outside Nigeria.')
                                    ->schema([
                                        TextInput::make('premium_monthly_price_usd')
                                            ->label('Monthly Display Price (USD)')
                                            ->placeholder('e.g. $9.99')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_monthly_amount_usd')
                                            ->label('Monthly Charge Amount (Cents)')
                                            ->numeric()
                                            ->placeholder('999')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),

                                        TextInput::make('premium_yearly_price_usd')
                                            ->label('Yearly Display Price (USD)')
                                            ->placeholder('e.g. $99.99')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                        TextInput::make('premium_yearly_amount_usd')
                                            ->label('Yearly Charge Amount (Cents)')
                                            ->numeric()
                                            ->placeholder('9999')
                                            ->dehydrated(fn(?string $state): bool => filled($state)),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('App Stores')
                            ->schema([
                                Section::make('Apple App Store')
                                    ->schema([
                                        static::addSecureRevealAction(
                                            TextInput::make('apple_shared_secret')
                                                ->label('App Store Shared Secret')
                                        ),
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
                                            ->disk('public')
                                            ->directory('sounds')
                                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg']),
                                        FileUpload::make('alarm_sound_path')
                                            ->label('Alarm Sound')
                                            ->disk('public')
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

                        Tabs\Tab::make('Feature Management')
                            ->schema([
                                Section::make('Module Access')
                                    ->description('Enable or disable major features of the mobile app.')
                                    ->schema([
                                        Toggle::make('is_rituals_enabled')
                                            ->label('Enable Rituals'),
                                        Toggle::make('is_teachings_enabled')
                                            ->label('Enable Teachings (Blog)'),
                                        Toggle::make('is_audios_enabled')
                                            ->label('Enable Audio Teachings'),
                                        Toggle::make('is_nima_sedani_enabled')
                                            ->label('Enable Nima Sedani'),
                                        Toggle::make('is_community_enabled')
                                            ->label('Enable Community'),
                                    ])->columns(2),

                                Section::make('Premium Overrides')
                                    ->description('Force entire modules to be premium-only, regardless of individual item settings.')
                                    ->schema([
                                        Toggle::make('is_rituals_premium_only')
                                            ->label('Rituals: Premium Only'),
                                        Toggle::make('is_teachings_premium_only')
                                            ->label('Teachings: Premium Only'),
                                        Toggle::make('is_audios_premium_only')
                                            ->label('Audios: Premium Only'),
                                        Toggle::make('is_nima_sedani_premium_only')
                                            ->label('Nima Sedani: Premium Only'),
                                    ])->columns(2),

                                Section::make('App Sync')
                                    ->description('Refresh the mobile app configuration. Use this after making changes to module access or pricing.')
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('sync_info')
                                            ->label('Current Version')
                                            ->content(fn($record) => "Config Version: " . ($record->app_config_version ?? 1)),
                                        Actions::make([
                                            Action::make('sync_app')
                                                ->label('Sync App Configuration')
                                                ->icon('heroicon-m-arrow-path')
                                                ->color('success')
                                                ->requiresConfirmation()
                                                ->action(function ($record) {
                                                    $record->increment('app_config_version');
                                                    Notification::make()
                                                        ->title('App Config Synced')
                                                        ->body('Configuration version incremented to ' . $record->app_config_version)
                                                        ->success()
                                                        ->send();
                                                }),
                                        ]),
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
                                            ->default('smtp'),
                                        TextInput::make('mail_host')
                                            ->label('SMTP Host')
                                            ->placeholder('smtp.mailtrap.io'),
                                        TextInput::make('mail_port')
                                            ->label('SMTP Port')
                                            ->placeholder('2525'),
                                        TextInput::make('mail_username')
                                            ->label('SMTP Username'),
                                        static::addSecureRevealAction(
                                            TextInput::make('mail_password')
                                                ->label('SMTP Password')
                                        ),
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

    protected static function addSecureRevealAction(TextInput $component): TextInput
    {
        return $component
            ->password()
            ->revealable()
            ->dehydrated(fn(?string $state): bool => filled($state))
            ->required(fn(string $operation): bool => $operation === 'create')
            ->suffixAction(
                Action::make('secureReveal')
                    ->icon('heroicon-m-lock-closed')
                    ->color('gray')
                    ->tooltip('Reveal with Password')
                    ->form([
                        TextInput::make('verify_password')
                            ->label('Admin Password')
                            ->password()
                            ->required()
                    ])
                    ->action(function (array $data, TextInput $component) {
                        if (!Hash::check($data['verify_password'], auth()->user()->getAuthPassword())) {
                            Notification::make()
                                ->title('Incorrect password')
                                ->danger()
                                ->send();

                            return;
                        }

                        $state = $component->getState();

                        Notification::make()
                            ->title('Key Revealed')
                            ->body("Value: {$state}")
                            ->success()
                            ->persistent()
                            ->send();
                    })
            );
    }
}

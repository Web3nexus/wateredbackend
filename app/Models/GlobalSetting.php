<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_description',
        'contact_email',
        'contact_phone',
        'social_links',
        'is_landing_page_enabled',
        'android_download_url',
        'ios_download_url',
        'logo_path',
        'favicon_path',
        'tagline',
        'hero_title',
        'hero_subtitle',
        'hero_description',
        'hero_cta_text',
        'hero_image',
        'about_title',
        'about_description',
        'about_quote',
        'rituals_title',
        'ritual_acceptance_title',
        'ritual_acceptance_description',
        'ritual_witnesses_title',
        'ritual_witnesses_description',
        'rituals_image',
        'primary_color',
        'secondary_color',
        'default_language',
        'supported_languages',
        'maintenance_mode',
        'is_ads_enabled',
        'ad_unit_id_android',
        'ad_unit_id_ios',
        'ads_screens',
        'stripe_public_key',
        'stripe_secret_key',
        'paystack_public_key',
        'paystack_secret_key',
        'flutterwave_public_key',
        'flutterwave_secret_key',
        'premium_monthly_id',
        'premium_yearly_id',
        'apple_shared_secret',
        'google_play_package_name',
        'google_play_service_account_json',
        'notification_sound_path',
        'alarm_sound_path',
        'privacy_policy',
        'terms_of_service',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    protected $hidden = [
        'stripe_secret_key',
        'paystack_secret_key',
        'flutterwave_secret_key',
        'apple_shared_secret',
        'google_play_service_account_json',
        'mail_password',
    ];

    protected $casts = [
        'social_links' => 'json',
        'supported_languages' => 'json',
        'maintenance_mode' => 'boolean',
        'is_landing_page_enabled' => 'boolean',
        'is_ads_enabled' => 'boolean',
        'ads_screens' => 'json',
    ];
}

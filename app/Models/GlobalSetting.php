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
        'premium_monthly_price',
        'premium_yearly_price',
        'premium_monthly_amount',
        'premium_yearly_amount',
        'paystack_monthly_plan_code',
        'paystack_yearly_plan_code',
        'premium_monthly_price_usd',
        'premium_yearly_price_usd',
        'premium_monthly_amount_usd',
        'premium_yearly_amount_usd',
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
        'system_currency',
        'currency_symbol',
        'currency_position',
        'premium_title',
        'premium_subtitle',
        'premium_features',
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
        'premium_features' => 'json',
    ];
    protected function logoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->logo_path)
                    return asset('images/watered-logo.png');
                if (filter_var($this->logo_path, FILTER_VALIDATE_URL))
                    return $this->logo_path;
                return asset(\Illuminate\Support\Facades\Storage::url($this->logo_path));
            }
        );
    }

    protected function faviconUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->favicon_path)
                    return asset('favicon.ico');
                if (filter_var($this->favicon_path, FILTER_VALIDATE_URL))
                    return $this->favicon_path;
                return asset(\Illuminate\Support\Facades\Storage::url($this->favicon_path));
            }
        );
    }

    protected function heroImageUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->hero_image)
                    return asset('images/lord-uzih-hero.png');
                if (filter_var($this->hero_image, FILTER_VALIDATE_URL))
                    return $this->hero_image;
                return \Illuminate\Support\Facades\Storage::disk('public')->url($this->hero_image);
            }
        );
    }

    protected function ritualsImageUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->rituals_image)
                    return asset('images/acceptance-ritual.png');
                if (filter_var($this->rituals_image, FILTER_VALIDATE_URL))
                    return $this->rituals_image;
                return \Illuminate\Support\Facades\Storage::disk('public')->url($this->rituals_image);
            }
        );
    }
}

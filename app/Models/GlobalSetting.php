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
        'logo_path',
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
        'notification_sound_path',
        'alarm_sound_path',
    ];

    protected $casts = [
        'social_links' => 'json',
        'supported_languages' => 'json',
        'maintenance_mode' => 'boolean',
        'is_ads_enabled' => 'boolean',
        'ads_screens' => 'json',
    ];
}

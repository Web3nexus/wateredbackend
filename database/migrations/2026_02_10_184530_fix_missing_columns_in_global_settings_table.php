<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $cols = [
                'tagline' => ['type' => 'string', 'nullable' => true],
                'hero_title' => ['type' => 'string', 'nullable' => true],
                'hero_subtitle' => ['type' => 'string', 'nullable' => true],
                'hero_description' => ['type' => 'text', 'nullable' => true],
                'hero_cta_text' => ['type' => 'string', 'nullable' => true],
                'hero_image' => ['type' => 'string', 'nullable' => true],
                'about_title' => ['type' => 'string', 'nullable' => true],
                'about_description' => ['type' => 'text', 'nullable' => true],
                'about_quote' => ['type' => 'text', 'nullable' => true],
                'rituals_title' => ['type' => 'string', 'nullable' => true],
                'ritual_acceptance_title' => ['type' => 'string', 'nullable' => true],
                'ritual_acceptance_description' => ['type' => 'text', 'nullable' => true],
                'ritual_witnesses_title' => ['type' => 'string', 'nullable' => true],
                'ritual_witnesses_description' => ['type' => 'text', 'nullable' => true],
                'rituals_image' => ['type' => 'string', 'nullable' => true],
                'android_download_url' => ['type' => 'string', 'nullable' => true],
                'ios_download_url' => ['type' => 'string', 'nullable' => true],
                'stripe_public_key' => ['type' => 'string', 'nullable' => true],
                'stripe_secret_key' => ['type' => 'string', 'nullable' => true],
                'paystack_public_key' => ['type' => 'string', 'nullable' => true],
                'paystack_secret_key' => ['type' => 'string', 'nullable' => true],
                'flutterwave_public_key' => ['type' => 'string', 'nullable' => true],
                'flutterwave_secret_key' => ['type' => 'string', 'nullable' => true],
                'premium_monthly_id' => ['type' => 'string', 'nullable' => true],
                'premium_yearly_id' => ['type' => 'string', 'nullable' => true],
            ];

            foreach ($cols as $name => $config) {
                if (!Schema::hasColumn('global_settings', $name)) {
                    $type = $config['type'];
                    $table->$type($name)->nullable($config['nullable'] ?? true);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
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
                'android_download_url',
                'ios_download_url',
                'stripe_public_key',
                'stripe_secret_key',
                'paystack_public_key',
                'paystack_secret_key',
                'flutterwave_public_key',
                'flutterwave_secret_key',
                'premium_monthly_id',
                'premium_yearly_id',
            ]);
        });
    }
};

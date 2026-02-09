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
            $table->string('apple_shared_secret')->nullable()->after('premium_yearly_id');
            $table->string('google_play_package_name')->nullable()->after('apple_shared_secret');
            $table->text('google_play_service_account_json')->nullable()->after('google_play_package_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'apple_shared_secret',
                'google_play_package_name',
                'google_play_service_account_json',
            ]);
        });
    }
};

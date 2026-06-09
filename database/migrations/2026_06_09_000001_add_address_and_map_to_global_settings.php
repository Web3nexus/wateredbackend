<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->text('address')->nullable()->after('social_links');
            $table->string('latitude', 20)->nullable()->after('address');
            $table->string('longitude', 20)->nullable()->after('latitude');
            $table->boolean('is_map_enabled')->default(false)->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude', 'is_map_enabled']);
        });
    }
};

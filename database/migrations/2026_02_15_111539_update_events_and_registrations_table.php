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
        Schema::table('events', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('title');
            $table->string('banner_image')->nullable()->after('location');
            $table->date('event_date')->nullable()->after('banner_image');
            $table->time('event_time')->nullable()->after('event_date');

            // If image_url exists, we can keep it for legacy or drop it. 
            // The prompt mentions banner_image specifically.
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('full_name')->nullable()->after('user_id');
            $table->string('email')->nullable()->after('full_name');
            $table->string('phone')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['slug', 'banner_image', 'event_date', 'event_time']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn(['full_name', 'email', 'phone']);
        });
    }
};

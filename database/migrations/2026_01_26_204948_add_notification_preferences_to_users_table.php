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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('push_notifications')->default(true);
            $table->boolean('ritual_reminders')->default(true);
            $table->boolean('event_updates')->default(true);
            $table->boolean('community_activity')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'push_notifications',
                'ritual_reminders',
                'event_updates',
                'community_activity'
            ]);
        });
    }
};

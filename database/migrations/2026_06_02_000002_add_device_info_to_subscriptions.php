<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'device_type')) {
                $table->string('device_type')->nullable()->after('platform');
            }
            if (!Schema::hasColumn('subscriptions', 'os_version')) {
                $table->string('os_version')->nullable()->after('device_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'os_version']);
        });
    }
};

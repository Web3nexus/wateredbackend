<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('global_settings')->update([
            'system_currency' => 'NGN',
            'currency_symbol' => '₦',
            'currency_position' => 'before',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse as we don't know the previous value, 
        // but we can default back to USD/$ if needed.
    }
};

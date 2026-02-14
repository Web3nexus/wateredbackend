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
        // Add sort_order column to traditions if it doesn't exist
        if (!Schema::hasColumn('traditions', 'sort_order')) {
            Schema::table('traditions', function (Blueprint $table) {
                $table->integer('sort_order')->default(999);
            });
        }

        // Rename "Four Witness" to "Watered"
        DB::table('traditions')
            ->where('name', 'Four Witness')
            ->orWhere('name', 'THE FOUR WITNESS')
            ->update([
                'name' => 'Watered',
                'slug' => 'watered',
                'sort_order' => 1,
            ]);

        // Ensure other traditions have a higher sort order
        DB::table('traditions')
            ->where('name', '!=', 'Watered')
            ->where('sort_order', 1)
            ->update(['sort_order' => 10]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('traditions')
            ->where('name', 'Watered')
            ->update([
                'name' => 'Four Witness',
                'slug' => 'four-witness',
                'sort_order' => 999,
            ]);

        if (Schema::hasColumn('traditions', 'sort_order')) {
            Schema::table('traditions', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};

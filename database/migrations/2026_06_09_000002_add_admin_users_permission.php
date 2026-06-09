<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate([
            'name' => 'access_admin_users',
            'guard_name' => 'admin',
        ]);
    }

    public function down(): void
    {
        Permission::where('name', 'access_admin_users')
            ->where('guard_name', 'admin')
            ->delete();
    }
};

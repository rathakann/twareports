<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'export.excel',
            'export.csv',
            'export.copy',
            'export.print',
            'export.all',
            'users.export',
            'roles.export',
            'permissions.export',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'export.excel',
                'export.csv',
                'export.copy',
                'export.print',
                'users.export',
                'roles.export',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'export.excel',
            'export.csv',
            'export.copy',
            'export.print',
            'export.all',
            'users.export',
            'roles.export',
            'permissions.export',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};

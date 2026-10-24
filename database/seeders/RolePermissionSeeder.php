<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.status',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'permissions.manage',
            'export.excel',
            'export.csv',
            'export.copy',
            'export.print',
            'export.all',
            'users.export',
            'roles.export',
            'permissions.export',
            // Purchase Reports
            'reports.purchase.purchase-orders.view',
            'reports.purchase.purchase-orders.export',
            'reports.purchase.ap-invoices.view',
            'reports.purchase.ap-invoices.export',
            'reports.purchase.outgoing-payments.view',
            'reports.purchase.outgoing-payments.export',
            // Sales Reports
            'reports.sales.purchase-orders.view',
            'reports.sales.purchase-orders.export',
            'reports.sales.ar-invoices.view',
            'reports.sales.ar-invoices.export',
            'reports.sales.incoming-payments.view',
            'reports.sales.incoming-payments.export',
            'reports.sales.deposits.view',
            'reports.sales.deposits.export',
            // Financial Reports
            'reports.financial.balance-sheet.view',
            'reports.financial.balance-sheet.export',
            'reports.financial.profit-loss.view',
            'reports.financial.profit-loss.export',
            'reports.financial.cash-flow.view',
            'reports.financial.cash-flow.export',
            'reports.financial.trial-balance.view',
            'reports.financial.trial-balance.export',
            'reports.financial.general-ledger.view',
            'reports.financial.general-ledger.export',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 1. Super Admin Role
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->syncPermissions(Permission::all());

        // 2. Admin Role
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions([
            'users.view',
            'users.create',
            'users.edit',
            'users.status',
            'users.export',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.export',
            'reports.purchase.purchase-orders.view',
            'reports.purchase.purchase-orders.export',
            'reports.purchase.ap-invoices.view',
            'reports.purchase.ap-invoices.export',
            'reports.purchase.outgoing-payments.view',
            'reports.purchase.outgoing-payments.export',
            'reports.sales.purchase-orders.view',
            'reports.sales.purchase-orders.export',
            'reports.sales.ar-invoices.view',
            'reports.sales.ar-invoices.export',
            'reports.sales.incoming-payments.view',
            'reports.sales.incoming-payments.export',
            'reports.sales.deposits.view',
            'reports.sales.deposits.export',
            'reports.financial.balance-sheet.view',
            'reports.financial.balance-sheet.export',
            'reports.financial.profit-loss.view',
            'reports.financial.profit-loss.export',
            'reports.financial.cash-flow.view',
            'reports.financial.cash-flow.export',
            'reports.financial.trial-balance.view',
            'reports.financial.trial-balance.export',
            'reports.financial.general-ledger.view',
            'reports.financial.general-ledger.export',
            'export.excel',
            'export.csv',
            'export.copy',
            'export.print',
        ]);

        // 3. User Role
        $userRole = Role::firstOrCreate([
            'name' => 'User',
            'guard_name' => 'web',
        ]);
        $userRole->syncPermissions([
            'users.view',
        ]);

        // Seed default Super Admin account if not present
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@twareports.test'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // Seed default Admin account
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@twareports.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Seed default regular User account
        $staffUser = User::firstOrCreate(
            ['email' => 'user@twareports.test'],
            [
                'name' => 'Standard Staff',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $staffUser->assignRole($userRole);
    }
}

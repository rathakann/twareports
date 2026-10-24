<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Support\ExportPermission;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test Super Admin has universal export permission.
     */
    public function test_super_admin_can_export_any_module_and_format(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $this->assertTrue(ExportPermission::canExport('users', 'excel', $superAdmin));
        $this->assertTrue(ExportPermission::canExport('users', 'csv', $superAdmin));
        $this->assertTrue(ExportPermission::canExport('roles', 'copy', $superAdmin));
        $this->assertTrue(ExportPermission::canExport('permissions', 'print', $superAdmin));
        $this->assertTrue(ExportPermission::canExport('anything', 'pdf', $superAdmin));

        // Test API request with length=-1
        $response = $this->actingAs($superAdmin)->getJson(route('users.index', [
            'length' => -1,
            'is_export' => 1,
            'export_format' => 'excel',
        ]));
        $response->assertOk();
    }

    /**
     * Test user without export permission cannot export and gets 403.
     */
    public function test_user_without_export_permission_gets_403_on_export(): void
    {
        $user = User::where('email', 'user@twareports.test')->first();

        $this->assertFalse(ExportPermission::canExport('users', 'excel', $user));
        $this->assertFalse(ExportPermission::canExport('users', 'csv', $user));

        // Normal DataTables paginated request succeeds (user has users.view)
        $normalResponse = $this->actingAs($user)->getJson(route('users.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]));
        $normalResponse->assertOk();

        // Export request fails with 403
        $exportResponse = $this->actingAs($user)->getJson(route('users.index', [
            'length' => -1,
            'is_export' => 1,
            'export_format' => 'excel',
        ]));
        $exportResponse->assertForbidden();
    }

    /**
     * Test dynamic format permission: user with export.excel can export excel but not csv.
     */
    public function test_user_with_specific_format_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['users.view', 'export.excel']);

        $this->assertTrue(ExportPermission::canExport('users', 'excel', $user));
        $this->assertFalse(ExportPermission::canExport('users', 'csv', $user));

        $excelResponse = $this->actingAs($user)->getJson(route('users.index', [
            'length' => -1,
            'is_export' => 1,
            'export_format' => 'excel',
        ]));
        $excelResponse->assertOk();

        $csvResponse = $this->actingAs($user)->getJson(route('users.index', [
            'length' => -1,
            'is_export' => 1,
            'export_format' => 'csv',
        ]));
        $csvResponse->assertForbidden();
    }

    /**
     * Test module-level isolation: user with users.export + export.excel cannot export roles.
     */
    public function test_module_scoped_export_permission_isolation(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['users.view', 'roles.view', 'users.export', 'export.excel']);

        $this->assertTrue(ExportPermission::canExport('users', 'excel', $user));
        $this->assertFalse(ExportPermission::canExport('roles', 'excel', $user));

        $usersResponse = $this->actingAs($user)->getJson(route('users.index', [
            'length' => -1,
            'is_export' => 1,
            'export_format' => 'excel',
        ]));
        $usersResponse->assertOk();

        $rolesResponse = $this->actingAs($user)->getJson(route('roles.index', [
            'length' => -1,
            'is_export' => 1,
            'export_format' => 'excel',
        ]));
        $rolesResponse->assertForbidden();
    }

    /**
     * Test global export.all permission gives access to all formats and modules.
     */
    public function test_export_all_permission_grants_all_formats(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['users.view', 'export.all']);

        $this->assertTrue(ExportPermission::canExport('users', 'excel', $user));
        $this->assertTrue(ExportPermission::canExport('users', 'csv', $user));
        $this->assertTrue(ExportPermission::canExport('users', 'copy', $user));
        $this->assertTrue(ExportPermission::canExport('users', 'print', $user));
    }

    /**
     * Test dynamically created permission e.g. users.export.pdf.
     */
    public function test_dynamically_created_export_permission(): void
    {
        Permission::create(['name' => 'users.export.pdf', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['users.view', 'users.export.pdf']);

        $this->assertTrue(ExportPermission::canExport('users', 'pdf', $user));
        $this->assertFalse(ExportPermission::canExport('users', 'excel', $user));
    }
}

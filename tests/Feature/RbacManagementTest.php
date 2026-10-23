<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test that the Users DataTable AJAX endpoint returns JSON and avoids N+1 queries.
     */
    public function test_users_datatable_ajax_endpoint_returns_json_and_prevents_n_plus_one_queries(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        // Create 10 users with roles and permissions
        $role = Role::findByName('User');
        $permission = Permission::findByName('users.view');

        $users = User::factory()->count(10)->create(['status' => 'active']);
        foreach ($users as $user) {
            $user->assignRole($role);
            $user->givePermissionTo($permission);
        }

        // Measure query count: eager loading User::with(['roles', 'permissions'])
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($superAdmin)
            ->getJson('/users', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk()
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'status',
                        'roles_list',
                        'permissions_list',
                        'action',
                    ],
                ],
            ]);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // For 10+ users, queries should be constant (count query, users query, roles relation, permissions relation)
        // rather than growing per-row (N+1). Query count must be strictly under 10.
        $this->assertLessThan(10, $queryCount);
    }

    /**
     * Test that the Users DataTable properly filters by role and status.
     */
    public function test_users_datatable_filters_by_role_and_status(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $activeAdmin = User::factory()->create(['status' => 'active']);
        $activeAdmin->assignRole('Admin');

        $inactiveUser = User::factory()->create(['status' => 'inactive']);
        $inactiveUser->assignRole('User');

        // Filter by role Admin
        $roleFiltered = $this->actingAs($superAdmin)
            ->getJson('/users?role=Admin', ['X-Requested-With' => 'XMLHttpRequest']);

        $roleFiltered->assertOk();
        $roleData = collect($roleFiltered->json('data'));
        $this->assertTrue($roleData->contains('id', $activeAdmin->id));
        $this->assertFalse($roleData->contains('id', $inactiveUser->id));

        // Filter by status inactive
        $statusFiltered = $this->actingAs($superAdmin)
            ->getJson('/users?status=inactive', ['X-Requested-With' => 'XMLHttpRequest']);

        $statusFiltered->assertOk();
        $statusData = collect($statusFiltered->json('data'));
        $this->assertTrue($statusData->contains('id', $inactiveUser->id));
        $this->assertFalse($statusData->contains('id', $activeAdmin->id));
    }

    /**
     * Test that the Users DataTable handles draw parameter, newly created user visibility, and search by name or email.
     */
    public function test_users_datatable_handles_draw_parameter_and_global_search(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        // 1. Create a new user through the standard store endpoint
        $storeResponse = $this->actingAs($superAdmin)->post('/users', [
            'name' => 'Sophea Vanna',
            'email' => 'sophea@twareports.test',
            'password' => 'SecurePass123!',
            'status' => 'active',
            'roles' => ['User'],
        ]);
        $storeResponse->assertRedirect(route('users.index'));

        // 2. Verify DataTables returns JSON with draw query parameter even without X-Requested-With header
        $response = $this->actingAs($superAdmin)
            ->get('/users?draw=1');

        $response->assertOk()
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);

        $data = collect($response->json('data'));
        $this->assertTrue(
            $data->contains('email', 'sophea@twareports.test'),
            'Newly created user must appear in the Users DataTable response.'
        );

        // 3. Search by name
        $searchNameResponse = $this->actingAs($superAdmin)
            ->getJson('/users?draw=2&search[value]=Sophea');

        $searchNameResponse->assertOk();
        $searchNameData = collect($searchNameResponse->json('data'));
        $this->assertTrue($searchNameData->contains('email', 'sophea@twareports.test'));

        // 4. Search by email
        $searchEmailResponse = $this->actingAs($superAdmin)
            ->getJson('/users?draw=3&search[value]=sophea@twareports.test');

        $searchEmailResponse->assertOk();
        $searchEmailData = collect($searchEmailResponse->json('data'));
        $this->assertTrue($searchEmailData->contains('email', 'sophea@twareports.test'));
    }

    /**
     * Test that Roles and Permissions DataTables endpoints return server-side JSON.
     */
    public function test_roles_and_permissions_datatables_return_server_side_json(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $rolesResponse = $this->actingAs($superAdmin)
            ->getJson('/roles', ['X-Requested-With' => 'XMLHttpRequest']);

        $rolesResponse->assertOk()
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data' => [
                    '*' => ['id', 'name', 'users_count', 'permissions_count', 'action'],
                ],
            ]);

        $permissionsResponse = $this->actingAs($superAdmin)
            ->getJson('/permissions', ['X-Requested-With' => 'XMLHttpRequest']);

        $permissionsResponse->assertOk()
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data' => [
                    '*' => ['id', 'name', 'module', 'roles_count', 'users_count', 'action'],
                ],
            ]);
    }

    /**
     * Test creating a user with both roles (syncRoles) and direct permissions (syncPermissions).
     */
    public function test_user_can_be_created_with_roles_and_direct_permissions(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->post('/users', [
            'name' => 'Finance Analyst',
            'email' => 'analyst@twareports.test',
            'password' => 'SecurePass123!',
            'status' => 'active',
            'roles' => ['User'],
            'permissions' => ['users.create', 'users.edit'],
        ]);

        $response->assertRedirect(route('users.index'));

        $createdUser = User::where('email', 'analyst@twareports.test')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('User'));
        $this->assertTrue($createdUser->hasDirectPermission('users.create'));
        $this->assertTrue($createdUser->hasDirectPermission('users.edit'));
        $this->assertFalse($createdUser->hasDirectPermission('roles.delete'));
    }

    /**
     * Test updating a user updates roles (syncRoles) and direct permissions (syncPermissions).
     */
    public function test_user_can_be_updated_with_roles_and_direct_permissions(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $targetUser = User::factory()->create(['status' => 'active']);
        $targetUser->assignRole('User');
        $targetUser->givePermissionTo('users.view');

        $response = $this->actingAs($superAdmin)->put('/users/'.$targetUser->id, [
            'name' => 'Updated User Name',
            'email' => $targetUser->email,
            'status' => 'active',
            'roles' => ['Admin'],
            'permissions' => ['permissions.manage'],
        ]);

        $response->assertRedirect(route('users.index'));

        $targetUser->refresh();
        $this->assertSame('Updated User Name', $targetUser->name);
        $this->assertTrue($targetUser->hasRole('Admin'));
        $this->assertFalse($targetUser->hasRole('User'));
        $this->assertTrue($targetUser->hasDirectPermission('permissions.manage'));
        $this->assertFalse($targetUser->hasDirectPermission('users.view'));
    }

    /**
     * Test toggling user status via PATCH endpoint.
     */
    public function test_user_status_can_be_toggled_via_patch(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();
        $staffUser = User::where('email', 'user@twareports.test')->first();

        $this->assertSame('active', $staffUser->status);

        $response = $this->actingAs($superAdmin)->patchJson('/users/'.$staffUser->id.'/status', [
            'status' => 'inactive',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'inactive',
            ]);

        $this->assertSame('inactive', $staffUser->fresh()->status);
    }

    /**
     * Test that an inactive user is logged out, session invalidated, and redirected to login.
     */
    public function test_inactive_user_is_logged_out_and_blocked_from_protected_blade_routes(): void
    {
        $user = User::factory()->create(['status' => 'inactive']);
        $user->assignRole('User');

        // Attempting to access protected route when inactive
        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    /**
     * Anti-lockout: Test that a user cannot delete their own account (403 Forbidden).
     */
    public function test_self_deletion_attempt_is_rejected_with_403_forbidden(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->deleteJson('/users/'.$superAdmin->id);

        $response->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    /**
     * Anti-lockout: Test that a user cannot deactivate their own account (403 Forbidden).
     */
    public function test_self_deactivation_attempt_is_rejected_with_403_forbidden(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        // Via PATCH endpoint
        $patchResponse = $this->actingAs($superAdmin)->patchJson('/users/'.$superAdmin->id.'/status', [
            'status' => 'inactive',
        ]);
        $patchResponse->assertForbidden();
        $this->assertSame('active', $superAdmin->fresh()->status);

        // Via PUT update endpoint
        $putResponse = $this->actingAs($superAdmin)->put('/users/'.$superAdmin->id, [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'status' => 'inactive',
            'roles' => ['Super Admin'],
        ]);
        $putResponse->assertForbidden();
        $this->assertSame('active', $superAdmin->fresh()->status);
    }

    /**
     * Anti-lockout: Test that the primary Super Admin account and role cannot be deleted or revoked.
     */
    public function test_primary_super_admin_modification_is_blocked_with_403_forbidden(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        // Attempt to revoke Super Admin role from primary account (ID 1)
        $adminUser = User::where('email', 'admin@twareports.test')->first();
        $adminUser->givePermissionTo('users.edit');

        $revokeResponse = $this->actingAs($adminUser)->put('/users/'.$superAdmin->id, [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'status' => 'active',
            'roles' => ['User'], // Super Admin omitted
        ]);

        $revokeResponse->assertForbidden();
        $this->assertTrue($superAdmin->fresh()->hasRole('Super Admin'));

        // Attempt to delete Super Admin role itself
        $superAdminRole = Role::findByName('Super Admin');
        $deleteRoleResponse = $this->actingAs($superAdmin)->deleteJson('/roles/'.$superAdminRole->id);

        $deleteRoleResponse->assertForbidden();
        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
    }

    /**
     * Test filtering by created_date across users, roles, and permissions modules.
     */
    public function test_created_date_filtering_across_all_modules(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();
        $today = now()->toDateString();
        $pastDate = '2015-01-01';

        // 1. Users module created_date filter
        $usersMatchResponse = $this->actingAs($superAdmin)
            ->getJson("/users?draw=1&created_date={$today}", ['X-Requested-With' => 'XMLHttpRequest']);
        $usersMatchResponse->assertOk();
        $this->assertGreaterThan(0, $usersMatchResponse->json('recordsFiltered'));

        $usersNoMatchResponse = $this->actingAs($superAdmin)
            ->getJson("/users?draw=2&created_date={$pastDate}", ['X-Requested-With' => 'XMLHttpRequest']);
        $usersNoMatchResponse->assertOk();
        $this->assertEquals(0, $usersNoMatchResponse->json('recordsFiltered'));

        // 2. Roles module created_date filter
        $rolesMatchResponse = $this->actingAs($superAdmin)
            ->getJson("/roles?draw=1&created_date={$today}", ['X-Requested-With' => 'XMLHttpRequest']);
        $rolesMatchResponse->assertOk();
        $this->assertGreaterThan(0, $rolesMatchResponse->json('recordsFiltered'));

        $rolesNoMatchResponse = $this->actingAs($superAdmin)
            ->getJson("/roles?draw=2&created_date={$pastDate}", ['X-Requested-With' => 'XMLHttpRequest']);
        $rolesNoMatchResponse->assertOk();
        $this->assertEquals(0, $rolesNoMatchResponse->json('recordsFiltered'));

        // 3. Permissions module created_date filter
        $permissionsMatchResponse = $this->actingAs($superAdmin)
            ->getJson("/permissions?draw=1&created_date={$today}", ['X-Requested-With' => 'XMLHttpRequest']);
        $permissionsMatchResponse->assertOk();
        $this->assertGreaterThan(0, $permissionsMatchResponse->json('recordsFiltered'));

        $permissionsNoMatchResponse = $this->actingAs($superAdmin)
            ->getJson("/permissions?draw=2&created_date={$pastDate}", ['X-Requested-With' => 'XMLHttpRequest']);
        $permissionsNoMatchResponse->assertOk();
        $this->assertEquals(0, $permissionsNoMatchResponse->json('recordsFiltered'));
    }
}

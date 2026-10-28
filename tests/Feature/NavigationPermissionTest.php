<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Support\Navigation;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test Super Admin sees all navigation items including all Access Control links.
     */
    public function test_super_admin_sees_all_navigation_items(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('User Management');
        $response->assertSee('Roles');
        $response->assertSee('Permissions');
        $response->assertSee('Access Control');
    }

    /**
     * Test Admin sees User Management and Roles, but does not see Permissions.
     */
    public function test_admin_does_not_see_permissions_in_navigation(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('User Management');
        $response->assertSee('Roles');
        $response->assertDontSee('Permissions');
    }

    /**
     * Test Standard User (only users.view) sees User Management, but not Roles or Permissions.
     */
    public function test_standard_user_only_sees_user_management(): void
    {
        $user = User::where('email', 'user@twareports.test')->first();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('User Management');
        $response->assertDontSee('Roles');
        $response->assertDontSee('Permissions');
    }

    /**
     * Test user with no permissions in Access Control has the whole group hidden.
     */
    public function test_user_with_no_rbac_permissions_does_not_see_access_control_group(): void
    {
        $guestRole = Role::firstOrCreate(['name' => 'Restricted', 'guard_name' => 'web']);
        $restrictedUser = User::factory()->create([
            'status' => 'active',
        ]);
        $restrictedUser->assignRole($guestRole);

        $response = $this->actingAs($restrictedUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertDontSee('Access Control');
        $response->assertDontSee('User Management');
        $response->assertDontSee('Roles');
        $response->assertDontSee('Permissions');
    }

    /**
     * Test Navigation helper filter method directly.
     */
    public function test_navigation_filter_helper_removes_unauthorized_items_and_empty_groups(): void
    {
        $rawNavigation = config('navigation');

        $user = User::where('email', 'user@twareports.test')->first();
        $filtered = Navigation::filter($rawNavigation, $user);

        $groupLabels = collect($filtered)->pluck('label')->all();
        $this->assertContains('Workspace', $groupLabels);
        $this->assertNotContains('Components', $groupLabels);
        $this->assertContains('Access Control', $groupLabels);

        $accessGroup = collect($filtered)->firstWhere('label', 'Access Control');
        $itemLabels = collect($accessGroup['items'])->pluck('label')->all();

        $this->assertContains('User Management', $itemLabels);
        $this->assertNotContains('Roles', $itemLabels);
        $this->assertNotContains('Permissions', $itemLabels);
    }

    /**
     * Test horizontal navigation renders Report dropdown and reports for authorized users.
     */
    public function test_horizontal_navigation_renders_report_dropdown_and_modules(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('horizontal-nav', false);
        $response->assertSee('horizontal-report-dropdown', false);
        $response->assertSee('reports/purchase/purchase-orders', false);
        $response->assertSee('reports/sales/ar-invoices', false);
    }

    /**
     * Test navigation item is active on create page.
     */
    public function test_navigation_item_is_active_on_create_page(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('roles.create'));

        $response->assertOk();
        // The sidebar-item for roles should be marked is-active on roles.create
        $response->assertSee('is-active', false);
    }

    /**
     * Test navigation item is active on edit page.
     */
    public function test_navigation_item_is_active_on_edit_page(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('users.edit', $superAdmin));

        $response->assertOk();
        // The sidebar-item for users should be marked is-active on users.edit
        $response->assertSee('is-active', false);
    }

    /**
     * Test Navigation helper isRouteActive directly.
     */
    public function test_navigation_helper_is_route_active_for_resource_subroutes(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $this->actingAs($superAdmin)->get(route('roles.create'));
        $this->assertTrue(Navigation::isRouteActive('roles.index'));
        $this->assertFalse(Navigation::isRouteActive('users.index'));

        $this->actingAs($superAdmin)->get(route('users.edit', $superAdmin));
        $this->assertTrue(Navigation::isRouteActive('users.index'));
        $this->assertFalse(Navigation::isRouteActive('roles.index'));
    }
}

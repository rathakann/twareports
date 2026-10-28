<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\NavigationGroup;
use App\Models\User;
use App\Support\Navigation;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModuleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(ModuleSeeder::class);

        $this->superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $this->regularUser = User::factory()->create([
            'status' => 'active',
        ]);
    }

    /**
     * Test guests are redirected to login for administrator module routes.
     */
    public function test_guests_are_redirected_to_login_for_modules(): void
    {
        $response = $this->get(route('admin.modules.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.modules.create'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('admin.modules.store'), []);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test unauthorized users cannot view or manage modules.
     */
    public function test_unauthorized_users_receive_forbidden_status(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.modules.index'));
        $response->assertForbidden();

        $response = $this->actingAs($this->regularUser)->get(route('admin.modules.create'));
        $response->assertForbidden();

        $response = $this->actingAs($this->regularUser)->post(route('admin.modules.store'), [
            'group_label' => 'Test Group',
            'label' => 'Test Module',
        ]);
        $response->assertForbidden();
    }

    /**
     * Test super admin can view the modules index view.
     */
    public function test_super_admin_can_view_modules_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.modules.index'));

        $response->assertOk();
        $response->assertSee('Modules');
        $response->assertSee('Administrator');
        $response->assertSee('modules-table');
        $response->assertSee(route('admin.modules.create'));
    }

    /**
     * Test super admin can view the dedicated create module page.
     */
    public function test_super_admin_can_view_create_module_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.modules.create'));

        $response->assertOk();
        $response->assertSee('Create New Module');
        $response->assertSee('Navigation Group');
        $response->assertSee('Menu Label');
    }

    /**
     * Test super admin can view the dedicated edit module page.
     */
    public function test_super_admin_can_view_edit_module_page(): void
    {
        $module = Module::where('route', 'admin.modules.index')->firstOrFail();

        $response = $this->actingAs($this->superAdmin)->get(route('admin.modules.edit', $module));

        $response->assertOk();
        $response->assertSee('Edit Module: '.$module->label);
        $response->assertSee('Update Module');
    }

    /**
     * Test Yajra DataTables AJAX request returns JSON rows.
     */
    public function test_yajra_datatables_returns_json_response(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('admin.modules.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);

        $this->assertGreaterThan(0, $response->json('recordsTotal'));
    }

    /**
     * Test creating a new module via web form submission.
     */
    public function test_super_admin_can_create_a_module(): void
    {
        $data = [
            'group_label' => 'Administrator',
            'label' => 'Audit Logs',
            'icon' => 'clipboard-list',
            'route' => 'admin.audit-logs.index',
            'permission' => 'admin.audit.view',
            'sort_order' => 20,
            'is_active' => '1',
            'description' => 'System audit trail logs',
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.modules.store'), $data);

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modules', [
            'group_label' => 'Administrator',
            'label' => 'Audit Logs',
            'route' => 'admin.audit-logs.index',
        ]);
    }

    /**
     * Test viewing a module details via JSON using both UUID and numeric ID.
     */
    public function test_super_admin_can_view_module_details(): void
    {
        $module = Module::where('route', 'admin.modules.index')->firstOrFail();

        // View via model / UUID
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('admin.modules.show', $module));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'label' => 'Modules',
            ],
        ]);

        // View via numeric ID fallback
        $responseNumeric = $this->actingAs($this->superAdmin)
            ->getJson("/admin/modules/{$module->id}");

        $responseNumeric->assertOk();
        $responseNumeric->assertJson([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'label' => 'Modules',
            ],
        ]);
    }

    /**
     * Test toggling module active status.
     */
    public function test_super_admin_can_toggle_module_status(): void
    {
        $module = Module::where('route', 'admin.modules.index')->firstOrFail();
        $this->assertTrue($module->is_active);

        // Toggle to inactive
        $response = $this->actingAs($this->superAdmin)
            ->patchJson(route('admin.modules.status', $module), [
                'is_active' => false,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_active' => false,
        ]);
        $this->assertFalse($module->fresh()->is_active);

        // Toggle back to active without explicit payload
        $response = $this->actingAs($this->superAdmin)
            ->patchJson(route('admin.modules.status', $module));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_active' => true,
        ]);
        $this->assertTrue($module->fresh()->is_active);
    }

    /**
     * Test updating an existing module via web form submission.
     */
    public function test_super_admin_can_update_a_module(): void
    {
        $module = Module::where('route', 'admin.modules.index')->firstOrFail();

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.modules.update', $module), [
                'group_label' => 'Administrator',
                'label' => 'System Modules',
                'icon' => 'blocks',
                'route' => 'admin.modules.index',
                'permission' => 'admin.modules.view',
                'sort_order' => 15,
                'is_active' => '1',
                'description' => 'Updated module description',
            ]);

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'label' => 'System Modules',
            'sort_order' => 15,
        ]);
    }

    /**
     * Test quick reordering of modules.
     */
    public function test_super_admin_can_reorder_modules(): void
    {
        $module1 = Module::where('group_label', 'Master Data')->whereNotNull('parent_id')->orderBy('sort_order')->firstOrFail();

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('admin.modules.reorder'), [
                'id' => $module1->id,
                'direction' => 'down',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    /**
     * Test deleting a module.
     */
    public function test_super_admin_can_delete_a_module(): void
    {
        $module = Module::create([
            'group_label' => 'Custom',
            'label' => 'Temporary Module',
            'route' => 'temp.index',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson(route('admin.modules.destroy', $module));

        $response->assertOk();
        $this->assertDatabaseMissing('modules', [
            'id' => $module->id,
        ]);
    }

    /**
     * Test Navigation::getTree reflects modules dynamically.
     */
    public function test_navigation_tree_reflects_database_modules(): void
    {
        $tree = Navigation::getTree();

        $this->assertIsArray($tree);
        $groups = array_column($tree, 'label');

        $this->assertContains('Master Data', $groups);
        $this->assertContains('Administrator', $groups);

        $adminGroup = collect($tree)->firstWhere('label', 'Administrator');
        $this->assertNotNull($adminGroup);

        $adminLabels = array_column($adminGroup['items'], 'label');
        $this->assertContains('Modules', $adminLabels);
    }

    /**
     * Test super admin can list navigation groups.
     */
    public function test_super_admin_can_list_navigation_groups(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('admin.navigation-groups.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'groups' => [
                '*' => ['id', 'label', 'icon', 'sort_order', 'is_active', 'modules_count'],
            ],
        ]);
    }

    /**
     * Test super admin can create a new navigation group.
     */
    public function test_super_admin_can_create_navigation_group(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('admin.navigation-groups.store'), [
                'label' => 'Warehouse & Inventory',
                'icon' => 'boxes',
                'sort_order' => 65,
                'is_active' => true,
            ]);

        $response->assertCreated();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('navigation_groups', [
            'label' => 'Warehouse & Inventory',
            'icon' => 'boxes',
            'sort_order' => 65,
        ]);
    }

    /**
     * Test super admin can update a navigation group and synchronize linked modules.
     */
    public function test_super_admin_can_update_navigation_group_and_sync_modules(): void
    {
        $group = NavigationGroup::create([
            'uuid' => (string) Str::uuid(),
            'label' => 'Procurement',
            'icon' => 'shopping-cart',
            'sort_order' => 70,
            'is_active' => true,
        ]);

        $module = Module::create([
            'navigation_group_id' => $group->id,
            'group_label' => 'Procurement',
            'label' => 'Suppliers',
            'route' => 'suppliers.index',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->putJson(route('admin.navigation-groups.update', $group), [
                'label' => 'Purchasing & Sourcing',
                'icon' => 'shopping-bag',
                'sort_order' => 75,
                'is_active' => true,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('navigation_groups', [
            'id' => $group->id,
            'label' => 'Purchasing & Sourcing',
            'icon' => 'shopping-bag',
            'sort_order' => 75,
        ]);

        // Module should have updated group_label and group_order
        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'navigation_group_id' => $group->id,
            'group_label' => 'Purchasing & Sourcing',
            'group_order' => 75,
        ]);
    }

    /**
     * Test deleting a navigation group with modules fails, but empty group succeeds.
     */
    public function test_navigation_group_deletion_rules(): void
    {
        $groupWithModules = NavigationGroup::where('label', 'Master Data')->first();
        if ($groupWithModules) {
            $failResponse = $this->actingAs($this->superAdmin)
                ->deleteJson(route('admin.navigation-groups.destroy', $groupWithModules));

            $failResponse->assertStatus(422);
        }

        $emptyGroup = NavigationGroup::create([
            'uuid' => (string) Str::uuid(),
            'label' => 'Empty Group',
            'icon' => 'folder',
            'sort_order' => 999,
            'is_active' => true,
        ]);

        $successResponse = $this->actingAs($this->superAdmin)
            ->deleteJson(route('admin.navigation-groups.destroy', $emptyGroup));

        $successResponse->assertOk();
        $this->assertDatabaseMissing('navigation_groups', ['id' => $emptyGroup->id]);
    }

    /**
     * Test super admin can access the dedicated navigation groups page and submit forms.
     */
    public function test_super_admin_can_manage_navigation_groups_via_web_interface(): void
    {
        // 1. View page
        $pageResponse = $this->actingAs($this->superAdmin)
            ->get(route('admin.navigation-groups.index'));

        $pageResponse->assertOk();
        $pageResponse->assertSee('Navigation Groups');
        $pageResponse->assertSee('Configured Navigation Groups');

        // 2. Create via web form
        $createResponse = $this->actingAs($this->superAdmin)
            ->post(route('admin.navigation-groups.store'), [
                'label' => 'Logistics & Fleet',
                'icon' => 'truck',
                'sort_order' => 88,
            ]);

        $createResponse->assertRedirect(route('admin.navigation-groups.index'));
        $createResponse->assertSessionHas('success');

        $created = NavigationGroup::where('label', 'Logistics & Fleet')->firstOrFail();
        $this->assertEquals('truck', $created->icon);

        // 3. Update via web form
        $updateResponse = $this->actingAs($this->superAdmin)
            ->put(route('admin.navigation-groups.update', $created), [
                'label' => 'Fleet Management',
                'icon' => 'car',
                'sort_order' => 89,
                'is_active' => '1',
            ]);

        $updateResponse->assertRedirect(route('admin.navigation-groups.index'));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('navigation_groups', [
            'id' => $created->id,
            'label' => 'Fleet Management',
            'icon' => 'car',
        ]);

        // 4. Delete via web form
        $deleteResponse = $this->actingAs($this->superAdmin)
            ->delete(route('admin.navigation-groups.destroy', $created));

        $deleteResponse->assertRedirect(route('admin.navigation-groups.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('navigation_groups', ['id' => $created->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UuidRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test models automatically receive a UUID on creation.
     */
    public function test_models_automatically_receive_uuid_on_creation(): void
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->uuid);
        $this->assertTrue(Str::isUuid($user->uuid));

        $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $this->assertNotNull($role->uuid);
        $this->assertTrue(Str::isUuid($role->uuid));

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'Test Report',
            'content' => 'Content',
        ]);
        $this->assertNotNull($report->uuid);
        $this->assertTrue(Str::isUuid($report->uuid));

        $po = PurchaseOrder::create([
            'doc_num' => 'PO-99999',
            'vendor_code' => 'VEND01',
            'vendor_name' => 'Vendor One',
            'doc_date' => now(),
            'currency' => 'USD',
            'subtotal' => 100,
            'tax_amount' => 10,
            'doc_total' => 110,
            'status' => 'Open',
        ]);
        $this->assertNotNull($po->uuid);
        $this->assertTrue(Str::isUuid($po->uuid));
    }

    /**
     * Test route helpers generate URLs with UUID instead of numeric ID.
     */
    public function test_route_helpers_generate_urls_with_uuid(): void
    {
        $user = User::factory()->create();
        $editUrl = route('users.edit', $user);

        $this->assertStringContainsString($user->uuid, $editUrl);
        $this->assertStringNotContainsString('/users/'.$user->id.'/edit', $editUrl);

        $role = Role::findByName('Admin');
        $roleEditUrl = route('roles.edit', $role);

        $this->assertStringContainsString($role->uuid, $roleEditUrl);
        $this->assertStringNotContainsString('/roles/'.$role->id.'/edit', $roleEditUrl);
    }

    /**
     * Test accessing resource URLs with numeric IDs returns 404 to prevent enumeration / guessing attacks.
     */
    public function test_numeric_id_in_url_returns_404_not_found(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();
        $targetUser = User::factory()->create();

        // Numeric ID for users
        $response = $this->actingAs($superAdmin)->get('/users/'.$targetUser->id.'/edit');
        $response->assertNotFound();

        // Random numeric ID
        $responseRandom = $this->actingAs($superAdmin)->get('/users/99999/edit');
        $responseRandom->assertNotFound();

        // Numeric ID for roles
        $adminRole = Role::findByName('Admin');
        $responseRole = $this->actingAs($superAdmin)->get('/roles/'.$adminRole->id.'/edit');
        $responseRole->assertNotFound();

        // Numeric ID for purchase orders report modal fetch
        $po = PurchaseOrder::create([
            'doc_num' => 'PO-12345',
            'vendor_code' => 'VEND02',
            'vendor_name' => 'Vendor Two',
            'doc_date' => now(),
            'currency' => 'USD',
            'subtotal' => 200,
            'tax_amount' => 20,
            'doc_total' => 220,
            'status' => 'Open',
        ]);
        $responsePo = $this->actingAs($superAdmin)->getJson('/reports/purchase/purchase-orders/'.$po->id);
        $responsePo->assertNotFound();

        // Numeric ID for API reports
        $token = $superAdmin->createToken('access_token', ['access-api'])->plainTextToken;
        $report = Report::factory()->create(['user_id' => $superAdmin->id]);

        $apiResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/reports/'.$report->id);
        $apiResponse->assertNotFound();
    }

    /**
     * Test accessing resource URLs with random non-UUID strings returns 404.
     */
    public function test_random_non_uuid_string_returns_404(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->get('/users/random-string-slug/edit');
        $response->assertNotFound();

        $responseRole = $this->actingAs($superAdmin)->get('/roles/admin-slug/edit');
        $responseRole->assertNotFound();
    }

    /**
     * Test valid UUID properly resolves model on web and API endpoints.
     */
    public function test_valid_uuid_properly_resolves_model(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();
        $targetUser = User::factory()->create();

        // Web user edit resolves
        $response = $this->actingAs($superAdmin)->get('/users/'.$targetUser->uuid.'/edit');
        $response->assertOk();

        // Web role edit resolves
        $role = Role::findByName('Admin');
        $responseRole = $this->actingAs($superAdmin)->get('/roles/'.$role->uuid.'/edit');
        $responseRole->assertOk();

        // Report modal show resolves
        $po = PurchaseOrder::create([
            'doc_num' => 'PO-54321',
            'vendor_code' => 'VEND03',
            'vendor_name' => 'Vendor Three',
            'doc_date' => now(),
            'currency' => 'USD',
            'subtotal' => 300,
            'tax_amount' => 30,
            'doc_total' => 330,
            'status' => 'Open',
        ]);
        $responsePo = $this->actingAs($superAdmin)->getJson('/reports/purchase/purchase-orders/'.$po->uuid);
        $responsePo->assertOk()
            ->assertJsonPath('data.doc_num', 'PO-54321');

        // API report show resolves
        $token = $superAdmin->createToken('access_token', ['access-api'])->plainTextToken;
        $report = Report::factory()->create(['user_id' => $superAdmin->id, 'title' => 'UUID Secured Report']);

        $apiResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/reports/'.$report->uuid);
        $apiResponse->assertOk()
            ->assertJsonPath('report.title', 'UUID Secured Report')
            ->assertJsonPath('report.uuid', $report->uuid);
    }
}

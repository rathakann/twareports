<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * A basic test example.
     */
    public function test_the_root_route_redirects_to_the_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirectToRoute('dashboard');
    }

    public function test_guests_are_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_the_dashboard_renders_the_application_layout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('TWA Reports');
        $response->assertSee('Dashboard');
    }

    public function test_the_component_showcase_renders_the_reusable_library(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/theme/components');

        $response->assertOk();
        $response->assertSee('UI Components');
        $response->assertSee('Management table');
    }

    public function test_the_example_listing_renders_static_demonstration_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/theme/listing');

        $response->assertOk();
        $response->assertSee('Example Listing');
        $response->assertSee('DEMO-001');
    }

    public function test_the_example_form_renders_the_safe_workflow(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/theme/form');

        $response->assertOk();
        $response->assertSee('Validation demonstration only');
    }

    public function test_the_example_form_validates_required_demonstration_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/theme/form', []);

        $response->assertSessionHasErrors(['name', 'category']);
    }

    public function test_the_example_form_confirms_validation_without_persisting_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/theme/form', [
            'name' => 'Quarterly demonstration',
            'category' => 'operations',
            'effective_date' => '2026-10-16',
            'notes' => 'Validation-only workflow.',
            'is_visible' => '1',
        ]);

        $response->assertRedirectToRoute('theme.form');
        $response->assertSessionHas('success', 'Demonstration validation passed. No data was saved.');
    }
}

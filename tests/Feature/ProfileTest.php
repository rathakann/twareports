<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test guests cannot access profile and are redirected to login.
     */
    public function test_guests_are_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get(route('profile.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated active user can view the profile page.
     */
    public function test_authenticated_active_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Developer',
            'email' => 'jane@example.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('Jane Developer');
        $response->assertSee('jane@example.com');
        $response->assertSee('Personal Information');
        $response->assertSee('Security');
        $response->assertSee('Roles');
        $response->assertSee('switchProfileTab');
        $response->assertDontSee('&quot;personal&quot;');
        $response->assertSee('"personal"', false);
    }

    /**
     * Test profile page displays assigned roles and capabilities.
     */
    public function test_profile_displays_assigned_roles_and_capabilities(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('Super Admin');
        $response->assertSee('Super Administrator Access');
        $response->assertSee('Effective Operational Permissions by Role');
    }

    /**
     * Test profile page groups operational permissions by role with human-friendly modules.
     */
    public function test_profile_displays_operational_permissions_grouped_by_role(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);
        $user->assignRole(['Admin', 'User']);

        $response = $this->actingAs($user)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('Effective Operational Permissions by Role');
        $response->assertSee('Admin');
        $response->assertSee('User');
        $response->assertSee('Financial Reports');
        $response->assertSee('Sales Reports');
        $response->assertSee('Purchase Reports');
        $response->assertSee('User Management');
    }

    /**
     * Test profile page displays direct user permissions when individually granted.
     */
    public function test_profile_displays_direct_user_permissions(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);
        $user->givePermissionTo('users.delete');

        $response = $this->actingAs($user)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('Direct User Permissions');
        $response->assertSee('User Accounts');
        $response->assertSee('Delete');
    }

    /**
     * Test user can update their personal name and email.
     */
    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success', 'Profile information updated successfully.');

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
    }

    /**
     * Test profile update fails when email is already taken by another user.
     */
    public function test_profile_update_fails_when_email_is_taken(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $user = User::factory()->create([
            'email' => 'myemail@example.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'My Name',
            'email' => 'existing@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        $user->refresh();
        $this->assertSame('myemail@example.com', $user->email);
    }

    /**
     * Test user can successfully change password with valid current password.
     */
    public function test_user_can_update_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentSecret123!'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'CurrentSecret123!',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'NewSecret456!',
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success', 'Password updated successfully.');

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecret456!', $user->password));
    }

    /**
     * Test password update fails with incorrect current password.
     */
    public function test_password_update_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectSecret123!'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'WrongPassword!',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'NewSecret456!',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('CorrectSecret123!', $user->password));
    }

    /**
     * Test password update fails when confirmation does not match.
     */
    public function test_password_update_fails_when_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectSecret123!'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'CorrectSecret123!',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'Mismatch123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test Profile link is accessible in user dropdown menu.
     */
    public function test_profile_link_is_accessible_in_user_dropdown(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('profile.index'));
        $response->assertSee('My Profile');
    }

    /**
     * Test user can delete their account profile with correct password.
     */
    public function test_user_can_delete_their_account_profile_with_correct_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('SecretPass123!'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'SecretPass123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test user cannot delete account profile with incorrect password.
     */
    public function test_user_cannot_delete_account_profile_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('SecretPass123!'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'WrongPassword!',
        ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Test the only active Super Admin cannot delete their account.
     */
    public function test_lone_super_admin_cannot_delete_their_account(): void
    {
        $superAdmin = User::where('email', 'superadmin@twareports.test')->first();

        $response = $this->actingAs($superAdmin)->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertAuthenticatedAs($superAdmin);
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }
}

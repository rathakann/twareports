<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    /**
     * Test guests cannot upload avatar and are redirected to login.
     */
    public function test_guests_cannot_upload_avatar(): void
    {
        $response = $this->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test guests cannot delete avatar and are redirected to login.
     */
    public function test_guests_cannot_delete_avatar(): void
    {
        $response = $this->delete(route('profile.avatar.destroy'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can upload avatar image via multipart file.
     */
    public function test_authenticated_user_can_upload_avatar_file(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('custom_avatar.png', 400, 400),
        ]);

        $user->refresh();

        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);

        $response->assertRedirect(route('profile.index', ['tab' => 'personal']));
        $response->assertSessionHas('status', 'profile-avatar-updated');
    }

    /**
     * Test authenticated user can upload cropped avatar via base64 data URL (AJAX request).
     */
    public function test_authenticated_user_can_upload_cropped_base64_avatar(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        // 1x1 transparent PNG base64
        $base64Png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('profile.avatar.update'), [
                'avatar_data' => $base64Png,
            ]);

        $user->refresh();

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Test uploading a new avatar deletes the previous avatar file from storage.
     */
    public function test_uploading_new_avatar_deletes_previous_file(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        // First upload
        $this->actingAs($user)->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar1.jpg', 300, 300),
        ]);
        $user->refresh();
        $firstAvatar = $user->avatar;
        Storage::disk('public')->assertExists($firstAvatar);

        // Second upload
        $this->actingAs($user)->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar2.jpg', 300, 300),
        ]);
        $user->refresh();
        $secondAvatar = $user->avatar;

        $this->assertNotEquals($firstAvatar, $secondAvatar);
        Storage::disk('public')->assertMissing($firstAvatar);
        Storage::disk('public')->assertExists($secondAvatar);
    }

    /**
     * Test user can delete their avatar and the file is purged from disk.
     */
    public function test_authenticated_user_can_delete_avatar(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($user)->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 300, 300),
        ]);
        $user->refresh();
        $avatarPath = $user->avatar;
        Storage::disk('public')->assertExists($avatarPath);

        // Delete avatar via AJAX
        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->deleteJson(route('profile.avatar.destroy'));

        $user->refresh();

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing($avatarPath);
    }

    /**
     * Test validation rejects invalid data when uploading avatar.
     */
    public function test_avatar_upload_fails_without_file_or_data(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('profile.avatar.update'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['avatar']);
    }

    /**
     * Test profile view renders image avatar when user has one.
     */
    public function test_profile_page_renders_avatar_image(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'avatar' => 'avatars/sample.png',
        ]);

        Storage::disk('public')->put('avatars/sample.png', 'test content');

        $response = $this->actingAs($user)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('avatars/sample.png');
        $response->assertSee('Crop Profile Photo');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login issues access and refresh tokens with proper abilities.
     */
    public function test_user_can_login_with_valid_credentials_and_receive_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'alex@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'alex@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'is_admin'],
                'token_type',
                'access_token',
                'refresh_token',
                'expires_in',
            ]);

        $accessTokenString = $response->json('access_token');
        $refreshTokenString = $response->json('refresh_token');

        $accessToken = PersonalAccessToken::findToken($accessTokenString);
        $refreshToken = PersonalAccessToken::findToken($refreshTokenString);

        $this->assertNotNull($accessToken);
        $this->assertNotNull($refreshToken);
        $this->assertTrue($accessToken->can('access-api'));
        $this->assertFalse($accessToken->can('issue-access-token'));
        $this->assertTrue($refreshToken->can('issue-access-token'));
        $this->assertFalse($refreshToken->can('access-api'));
    }

    /**
     * Test login failure with incorrect password returns generic error to prevent enumeration.
     */
    public function test_login_fails_with_invalid_credentials_and_returns_generic_error_message(): void
    {
        User::factory()->create([
            'email' => 'target@example.com',
            'password' => 'CorrectPassword123!',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'target@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertSame(
            'These credentials do not match our records.',
            $response->json('errors.email.0'),
        );
    }

    /**
     * Test login failure with nonexistent email returns identical generic error message.
     */
    public function test_login_fails_with_nonexistent_email_and_returns_identical_generic_error_message(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'doesnotexist@example.com',
            'password' => 'AnyPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertSame(
            'These credentials do not match our records.',
            $response->json('errors.email.0'),
        );
    }

    /**
     * Test login input validation requires valid email and password.
     */
    public function test_login_validation_requires_valid_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test rate limiting throttles repeated brute-force login attempts.
     */
    public function test_login_endpoint_enforces_rate_limiting(): void
    {
        RateLimiter::clear(
            Str::transliterate('victim@example.com|127.0.0.1')
        );

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'victim@example.com',
                'password' => 'guess-'.$i,
            ])->assertStatus(422);
        }

        $throttledResponse = $this->postJson('/api/auth/login', [
            'email' => 'victim@example.com',
            'password' => 'guess-blocked',
        ]);

        $throttledResponse->assertStatus(429);
    }

    /**
     * Test token rotation revokes old refresh token and issues fresh tokens.
     */
    public function test_user_can_refresh_token_and_old_refresh_token_is_revoked(): void
    {
        $user = User::factory()->create([
            'password' => 'KnownSecret123!',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'KnownSecret123!',
        ]);

        $originalRefreshToken = $loginResponse->json('refresh_token');

        $refreshResponse = $this->withHeader('Authorization', 'Bearer '.$originalRefreshToken)
            ->postJson('/api/auth/refresh');

        $refreshResponse->assertOk()
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'refresh_token',
                'expires_in',
            ]);

        $newAccessToken = $refreshResponse->json('access_token');
        $newRefreshToken = $refreshResponse->json('refresh_token');

        $this->assertNotSame($originalRefreshToken, $newRefreshToken);

        // Verify old refresh token was revoked from database
        $this->assertNull(PersonalAccessToken::findToken($originalRefreshToken));

        // Forget memoized guard state to simulate a fresh incoming request
        auth()->forgetGuards();

        // Replay attempt with old refresh token must be rejected with 401
        $replayResponse = $this->withHeader('Authorization', 'Bearer '.$originalRefreshToken)
            ->postJson('/api/auth/refresh');

        $replayResponse->assertUnauthorized();

        // Forget memoized guard state to simulate next incoming request
        auth()->forgetGuards();

        // New access token works on protected route
        $userResponse = $this->withHeader('Authorization', 'Bearer '.$newAccessToken)
            ->getJson('/api/user');

        $userResponse->assertOk();
    }

    /**
     * Test logout revokes current token.
     */
    public function test_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::factory()->create([
            'password' => 'Password123!',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $accessToken = $loginResponse->json('access_token');

        $logoutResponse = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/auth/logout');

        $logoutResponse->assertOk()
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertNull(PersonalAccessToken::findToken($accessToken));

        // Forget memoized guard state to simulate a fresh incoming request
        auth()->forgetGuards();

        // Submitting revoked token returns 401
        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->getJson('/api/user')
            ->assertUnauthorized();
    }

    /**
     * Test that user payloads and serialization never leak password hashes or secrets.
     */
    public function test_serialized_user_payloads_do_not_leak_password_hashes_or_secrets(): void
    {
        $user = User::factory()->create([
            'password' => 'SecretPlainTextPass!',
        ]);

        // Model toArray() check
        $arrayPayload = $user->toArray();
        $this->assertArrayNotHasKey('password', $arrayPayload);
        $this->assertArrayNotHasKey('remember_token', $arrayPayload);

        // Model toJson() check
        $jsonPayload = $user->toJson();
        $this->assertStringNotContainsString('password', $jsonPayload);
        $this->assertStringNotContainsString('remember_token', $jsonPayload);
        $this->assertStringNotContainsString('SecretPlainTextPass!', $jsonPayload);

        // Login response payload check
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'SecretPlainTextPass!',
        ]);

        $loginResponse->assertOk();
        $loginJson = $loginResponse->getContent();
        $this->assertStringNotContainsString('"password"', $loginJson);
        $this->assertStringNotContainsString('remember_token', $loginJson);

        // Authenticated user endpoint payload check
        $accessToken = $loginResponse->json('access_token');
        $userEndpointResponse = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->getJson('/api/user');

        $userEndpointResponse->assertOk();
        $userEndpointJson = $userEndpointResponse->getContent();
        $this->assertStringNotContainsString('password', $userEndpointJson);
        $this->assertStringNotContainsString('remember_token', $userEndpointJson);
    }
}

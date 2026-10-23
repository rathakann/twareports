<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test expired access tokens are rejected with 401 Unauthorized.
     */
    public function test_expired_access_token_is_rejected_with_401_unauthorized(): void
    {
        $user = User::factory()->create();

        // Create an access token that expired in the past
        $expiredToken = $user->createToken(
            'access_token',
            ['access-api'],
            now()->subMinutes(5),
        )->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$expiredToken)
            ->getJson('/api/user');

        $response->assertUnauthorized();
    }

    /**
     * Test tokens lacking required abilities are rejected with 401 Unauthorized.
     */
    public function test_token_with_invalid_abilities_is_rejected_with_401_unauthorized(): void
    {
        $user = User::factory()->create();

        // Issue a refresh-only token
        $refreshToken = $user->createToken(
            'refresh_token',
            ['issue-access-token'],
            now()->addMinutes(60),
        )->plainTextToken;

        // Attempting to access an access-api protected route with refresh-only token
        $userResponse = $this->withHeader('Authorization', 'Bearer '.$refreshToken)
            ->getJson('/api/user');

        $userResponse->assertUnauthorized()
            ->assertJson(['message' => 'Invalid ability provided.']);

        auth()->forgetGuards();

        // Issue an access token
        $accessToken = $user->createToken(
            'access_token',
            ['access-api'],
            now()->addMinutes(15),
        )->plainTextToken;

        // Attempting to hit the refresh endpoint with an access token (missing issue-access-token)
        $refreshResponse = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/auth/refresh');

        $refreshResponse->assertUnauthorized()
            ->assertJson(['message' => 'Invalid ability provided.']);
    }

    /**
     * Test unauthenticated access to protected routes is rejected with 401 Unauthorized.
     */
    public function test_unauthenticated_request_is_rejected_with_401_unauthorized(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();

        $reportsResponse = $this->getJson('/api/reports');

        $reportsResponse->assertUnauthorized();
    }

    /**
     * Test horizontal privilege escalation is rejected with 403 Forbidden.
     * User B must not be able to modify or delete User A's report.
     */
    public function test_horizontal_privilege_escalation_attempt_is_rejected_with_403_forbidden(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $reportA = Report::factory()->create([
            'user_id' => $userA->id,
            'title' => 'User A Confidential Financial Summary',
            'content' => 'Classified information.',
        ]);

        $tokenB = $userB->createToken('access_token', ['access-api'], now()->addMinutes(15))->plainTextToken;

        // User B attempts to update User A's report
        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$tokenB)
            ->putJson('/api/reports/'.$reportA->id, [
                'title' => 'Tampered Title by User B',
                'content' => 'Tampered content.',
            ]);

        $updateResponse->assertForbidden();

        // Ensure database state was untouched
        $this->assertDatabaseHas('reports', [
            'id' => $reportA->id,
            'title' => 'User A Confidential Financial Summary',
        ]);

        auth()->forgetGuards();

        // User B attempts to delete User A's report
        $deleteResponse = $this->withHeader('Authorization', 'Bearer '.$tokenB)
            ->deleteJson('/api/reports/'.$reportA->id);

        $deleteResponse->assertForbidden();

        $this->assertDatabaseHas('reports', [
            'id' => $reportA->id,
        ]);
    }

    /**
     * Test vertical privilege escalation is rejected with 403 Forbidden.
     * A non-admin user must not be able to access restricted administrator endpoints.
     */
    public function test_vertical_privilege_escalation_attempt_by_regular_user_is_rejected_with_403_forbidden(): void
    {
        $regularUser = User::factory()->create([
            'is_admin' => false,
        ]);

        $token = $regularUser->createToken('access_token', ['access-api'], now()->addMinutes(15))->plainTextToken;

        // Regular user attempts to access admin metrics endpoint
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/admin/metrics');

        $response->assertForbidden();
    }

    /**
     * Test authorized users can manage their own reports.
     */
    public function test_authorized_user_can_access_and_manage_their_own_reports(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('access_token', ['access-api'], now()->addMinutes(15))->plainTextToken;

        // Create report
        $createResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/reports', [
                'title' => 'Quarterly Audit 2026',
                'content' => 'Everything looks good.',
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('report.title', 'Quarterly Audit 2026');

        $reportId = $createResponse->json('report.id');

        auth()->forgetGuards();

        // Update own report
        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/reports/'.$reportId, [
                'title' => 'Quarterly Audit 2026 - Revised',
            ]);

        $updateResponse->assertOk()
            ->assertJsonPath('report.title', 'Quarterly Audit 2026 - Revised');
    }

    /**
     * Test authorized administrator can access admin metrics.
     */
    public function test_authorized_admin_can_access_admin_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('access_token', ['access-api'], now()->addMinutes(15))->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/admin/metrics');

        $response->assertOk()
            ->assertJsonStructure(['total_users', 'total_reports', 'admin_access'])
            ->assertJson(['admin_access' => true]);
    }
}

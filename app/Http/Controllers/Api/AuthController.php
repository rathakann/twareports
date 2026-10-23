<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate the user and issue access and refresh tokens.
     *
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $accessTokenTtl = (int) config('sanctum.access_token_ttl', 15);
        $refreshTokenTtl = (int) config('sanctum.refresh_token_ttl', 10080);

        $accessToken = $user->createToken(
            'access_token',
            ['access-api'],
            now()->addMinutes($accessTokenTtl),
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            ['issue-access-token'],
            now()->addMinutes($refreshTokenTtl),
        );

        return response()->json([
            'user' => UserResource::make($user),
            'token_type' => 'Bearer',
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'expires_in' => $accessTokenTtl * 60,
        ]);
    }

    /**
     * Rotate the refresh token and issue a fresh pair of tokens.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke the current refresh token to enforce single-use token rotation
        $request->user()->currentAccessToken()->delete();

        $accessTokenTtl = (int) config('sanctum.access_token_ttl', 15);
        $refreshTokenTtl = (int) config('sanctum.refresh_token_ttl', 10080);

        $accessToken = $user->createToken(
            'access_token',
            ['access-api'],
            now()->addMinutes($accessTokenTtl),
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            ['issue-access-token'],
            now()->addMinutes($refreshTokenTtl),
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'expires_in' => $accessTokenTtl * 60,
        ]);
    }

    /**
     * Revoke the current access or refresh token upon logout.
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->boolean('all_devices')) {
            $request->user()->tokens()->delete();
        } else {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Retrieve the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($request->user()),
        ]);
    }
}

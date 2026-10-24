<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\PermissionHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile and account settings.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->load(['roles.permissions', 'permissions']);

        $roles = $user->roles;
        $permissions = $user->getAllPermissions();
        $directPermissions = $user->getDirectPermissions();

        $rolePermissionsGrouped = [];
        foreach ($roles as $role) {
            $rolePermissionsGrouped[$role->id] = PermissionHelper::groupPermissions($role->permissions);
        }

        $directPermissionsGrouped = PermissionHelper::groupPermissions($directPermissions);

        $activeTab = $request->query('tab', session('active_tab', 'personal'));

        return view('profile.index', [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
            'directPermissions' => $directPermissions,
            'rolePermissionsGrouped' => $rolePermissionsGrouped,
            'directPermissionsGrouped' => $directPermissionsGrouped,
            'navigation' => config('navigation'),
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * Update the user's personal profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.index')
            ->with('success', 'Profile information updated successfully.')
            ->with('active_tab', 'personal');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password updated successfully.')
            ->with('active_tab', 'security');
    }
}

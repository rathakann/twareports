<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\PermissionHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
     * Upload and update the user's profile photo/avatar.
     */
    public function updateAvatar(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // 1. If cropped base64 image data is provided from client canvas crop
        if ($request->filled('avatar_data')) {
            $data = (string) $request->input('avatar_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $ext = strtolower($type[1]);
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = 'jpg';
                }

                $decodedData = base64_decode($data);
                if ($decodedData === false) {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Invalid image encoding.'], 422);
                    }

                    return back()->withErrors(['avatar' => 'Invalid image encoding.']);
                }

                $user->deleteAvatarFile();

                $filename = 'avatars/avatar_'.$user->id.'_'.time().'_'.Str::random(8).'.'.$ext;
                Storage::disk('public')->put($filename, $decodedData);

                $user->avatar = $filename;
                $user->save();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Profile photo updated successfully.',
                        'avatar_url' => $user->avatar_url,
                    ]);
                }

                return redirect()->route('profile.index', ['tab' => 'personal'])
                    ->with('status', 'profile-avatar-updated')
                    ->with('success', 'Profile photo updated successfully.')
                    ->with('active_tab', 'personal');
            }
        }

        // 2. If direct file upload is submitted
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $user->deleteAvatarFile();

            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $ext = 'jpg';
            }

            $filename = 'avatars/avatar_'.$user->id.'_'.time().'_'.Str::random(8).'.'.$ext;

            // On Windows / Laragon, $file->getRealPath() can return false for temporary upload files.
            // Reading via path directly avoids fopen('', 'r') ValueError exceptions.
            $realPath = $file->getRealPath();
            $pathName = $file->getPathname();
            $sourcePath = ($realPath && file_exists($realPath)) ? $realPath : (($pathName && file_exists($pathName)) ? $pathName : null);

            if ($sourcePath) {
                $content = @file_get_contents($sourcePath);
                if ($content !== false) {
                    Storage::disk('public')->put($filename, $content);
                    $path = $filename;
                } else {
                    $path = $file->store('avatars', 'public');
                }
            } else {
                $path = $file->store('avatars', 'public');
            }

            $user->avatar = $path;
            $user->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile photo updated successfully.',
                    'avatar_url' => $user->avatar_url,
                ]);
            }

            return redirect()->route('profile.index', ['tab' => 'personal'])
                ->with('status', 'profile-avatar-updated')
                ->with('success', 'Profile photo updated successfully.')
                ->with('active_tab', 'personal');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'No valid image provided.'], 422);
        }

        return back()->withErrors(['avatar' => 'Please provide a valid image file.']);
    }

    /**
     * Remove the user's custom profile avatar and revert to initials.
     */
    public function destroyAvatar(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->deleteAvatarFile();
        $user->avatar = null;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo removed successfully.',
            ]);
        }

        return redirect()->route('profile.index', ['tab' => 'personal'])
            ->with('status', 'profile-avatar-deleted')
            ->with('success', 'Profile photo removed successfully.')
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

    /**
     * Delete the authenticated user's account and profile permanently.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        /** @var User $user */
        $user = $request->user();

        // Safety: prevent deleting the only active Super Admin account
        if ($user->hasRole('Super Admin')) {
            $superAdminCount = User::role('Super Admin')->where('status', 'active')->count();
            if ($superAdminCount <= 1) {
                return back()->withErrors(['password' => 'Cannot delete the only remaining active Super Administrator account.'], 'userDeletion');
            }
        }

        $user->deleteAvatarFile();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Your account profile has been successfully deleted.');
    }
}

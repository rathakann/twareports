<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.view', only: ['index', 'show']),
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.edit', only: ['edit', 'update']),
            new Middleware('permission:users.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of users or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('users.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('users', $request->input('export_format'));
            }

            $query = User::with(['roles', 'permissions'])->select('users.*');

            if ($request->filled('role') && $request->role !== 'all') {
                $query->whereHas('roles', function ($q) use ($request): void {
                    $q->where('name', $request->role);
                });
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('created_date')) {
                $date = trim($request->created_date);
                if (str_contains($date, ' to ')) {
                    [$start, $end] = explode(' to ', $date);
                    $query->whereDate('users.created_at', '>=', trim($start))
                        ->whereDate('users.created_at', '<=', trim($end));
                } else {
                    $query->whereDate('users.created_at', $date);
                }
            }

            return DataTables::of($query)
                ->filterColumn('name', function ($query, $keyword): void {
                    $query->where(function ($q) use ($keyword): void {
                        $q->where('users.name', 'like', "%{$keyword}%")
                            ->orWhere('users.email', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('roles_list', function (User $user): array {
                    return $user->roles->pluck('name')->toArray();
                })
                ->addColumn('permissions_list', function (User $user): array {
                    return $user->permissions->pluck('name')->toArray();
                })
                ->addColumn('action', function (User $user): string {
                    return view('users.partials.actions', compact('user'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $roles = Role::orderBy('name')->get();
        $navigation = config('navigation');

        return view('users.index', compact('roles', 'navigation'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('users.create');

        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(function (Permission $permission): string {
            return explode('.', $permission->name)[0] ?? 'general';
        });
        $navigation = config('navigation');

        return view('users.create', compact('roles', 'permissions', 'navigation'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'is_admin' => in_array('Super Admin', $validated['roles'] ?? []) || in_array('Admin', $validated['roles'] ?? []),
        ]);

        if (! empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        if (! empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user->load(['roles', 'permissions']),
            ], 201);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        Gate::authorize('users.edit');

        $user->load(['roles.permissions', 'permissions']);
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(function (Permission $permission): string {
            return explode('.', $permission->name)[0] ?? 'general';
        });

        $inheritedPermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $directPermissions = $user->permissions->pluck('name')->toArray();
        $navigation = config('navigation');

        return view('users.edit', compact(
            'user',
            'roles',
            'permissions',
            'inheritedPermissions',
            'directPermissions',
            'navigation'
        ));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        // Anti-lockout safeguards
        if ((int) $user->id === (int) $request->user()?->id && $validated['status'] === 'inactive') {
            abort(403, 'You cannot deactivate your own account.');
        }

        if ($user->hasRole('Super Admin') && (int) $user->id === 1) {
            $roles = $validated['roles'] ?? [];
            if (! in_array('Super Admin', $roles)) {
                abort(403, 'You cannot remove the Super Admin role from the primary Super Admin account.');
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['roles'])) {
            $updateData['is_admin'] = in_array('Super Admin', $validated['roles']) || in_array('Admin', $validated['roles']);
            $user->syncRoles($validated['roles']);
        }

        if (isset($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        $user->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user->fresh(['roles', 'permissions']),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse|JsonResponse
    {
        Gate::authorize('users.delete');

        // Anti-lockout safeguard: Cannot delete self
        if ((int) $user->id === (int) $request->user()?->id) {
            abort(403, 'You cannot delete your own account.');
        }

        // Anti-lockout safeguard: Cannot delete primary Super Admin
        if ($user->hasRole('Super Admin') && (int) $user->id === 1) {
            abort(403, 'The primary Super Admin account cannot be deleted.');
        }

        $user->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle a user's active/inactive status via AJAX PATCH.
     */
    public function toggleStatus(Request $request, User $user): JsonResponse
    {
        Gate::authorize('users.status');

        $targetStatus = $request->input('status', $user->isActive() ? 'inactive' : 'active');

        // Anti-lockout safeguard: Cannot deactivate self
        if ((int) $user->id === (int) $request->user()?->id && $targetStatus === 'inactive') {
            abort(403, 'You cannot deactivate your own account.');
        }

        // Anti-lockout safeguard: Cannot deactivate primary Super Admin
        if ($user->hasRole('Super Admin') && (int) $user->id === 1 && $targetStatus === 'inactive') {
            abort(403, 'The primary Super Admin account cannot be deactivated.');
        }

        $user->update(['status' => $targetStatus]);

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => "User status updated to {$user->status}.",
        ]);
    }
}

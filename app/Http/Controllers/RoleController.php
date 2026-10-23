<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.view', only: ['index', 'show']),
            new Middleware('permission:roles.create', only: ['create', 'store']),
            new Middleware('permission:roles.edit', only: ['edit', 'update']),
            new Middleware('permission:roles.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of roles or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('roles.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('roles', $request->input('export_format'));
            }

            $query = Role::withCount(['users', 'permissions']);

            if ($request->filled('guard') && $request->guard !== 'all') {
                $query->where('guard_name', $request->guard);
            }

            if ($request->filled('assigned') && $request->assigned !== 'all') {
                if ($request->assigned === 'with_users') {
                    $query->has('users');
                } elseif ($request->assigned === 'without_users') {
                    $query->doesntHave('users');
                }
            }

            if ($request->filled('created_date')) {
                $date = trim($request->created_date);
                if (str_contains($date, ' to ')) {
                    [$start, $end] = explode(' to ', $date);
                    $query->whereDate('roles.created_at', '>=', trim($start))
                        ->whereDate('roles.created_at', '<=', trim($end));
                } else {
                    $query->whereDate('roles.created_at', $date);
                }
            }

            return DataTables::of($query)
                ->addColumn('action', function (Role $role): string {
                    return view('roles.partials.actions', compact('role'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $guards = Role::distinct()->pluck('guard_name')->sort()->values();
        $navigation = config('navigation');

        return view('roles.index', compact('navigation', 'guards'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        Gate::authorize('roles.create');

        $permissions = Permission::orderBy('name')->get()->groupBy(function (Permission $permission): string {
            return explode('.', $permission->name)[0] ?? 'general';
        });
        $navigation = config('navigation');

        return view('roles.create', compact('permissions', 'navigation'));
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleRequest $request): RedirectResponse|JsonResponse
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'role' => $role->load('permissions'),
            ], 201);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        Gate::authorize('roles.edit');

        $role->load('permissions');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::orderBy('name')->get()->groupBy(function (Permission $permission): string {
            return explode('.', $permission->name)[0] ?? 'general';
        });
        $navigation = config('navigation');

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'navigation'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse|JsonResponse
    {
        if ($role->name === 'Super Admin' && $request->name !== 'Super Admin') {
            abort(403, 'The Super Admin role cannot be renamed.');
        }

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->input('permissions', []));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'role' => $role->fresh('permissions'),
            ]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, Role $role): RedirectResponse|JsonResponse
    {
        Gate::authorize('roles.delete');

        if ($role->name === 'Super Admin') {
            abort(403, 'The Super Admin role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            abort(403, 'Cannot delete a role that is currently assigned to users.');
        }

        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:permissions.view|permissions.manage', only: ['index', 'show']),
            new Middleware('permission:permissions.create|permissions.manage', only: ['create', 'store']),
            new Middleware('permission:permissions.edit|permissions.manage', only: ['edit', 'update']),
            new Middleware('permission:permissions.delete|permissions.manage', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of permissions or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        abort_unless(Gate::allows('permissions.view') || Gate::allows('permissions.manage'), 403);

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('permissions', $request->input('export_format'));
            }

            $query = Permission::withCount(['roles', 'users']);

            if ($request->filled('module') && $request->module !== 'all') {
                $query->where('name', 'like', $request->module.'.%');
            }

            if ($request->filled('guard') && $request->guard !== 'all') {
                $query->where('guard_name', $request->guard);
            }

            if ($request->filled('created_date')) {
                $date = trim($request->created_date);
                if (str_contains($date, ' to ')) {
                    [$start, $end] = explode(' to ', $date);
                    $query->whereDate('permissions.created_at', '>=', trim($start))
                        ->whereDate('permissions.created_at', '<=', trim($end));
                } else {
                    $query->whereDate('permissions.created_at', $date);
                }
            }

            return DataTables::of($query)
                ->addColumn('module', function (Permission $permission): string {
                    return explode('.', $permission->name)[0] ?? 'general';
                })
                ->addColumn('action', function (Permission $permission): string {
                    return view('permissions.partials.actions', compact('permission'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $modules = Permission::pluck('name')
            ->map(fn (string $name): string => explode('.', $name)[0] ?? 'general')
            ->unique()
            ->sort()
            ->values();

        $guards = Permission::distinct()->pluck('guard_name')->sort()->values();
        $navigation = config('navigation');

        return view('permissions.index', compact('navigation', 'modules', 'guards'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): View
    {
        abort_unless(Gate::allows('permissions.create') || Gate::allows('permissions.manage'), 403);

        $navigation = config('navigation');

        return view('permissions.create', compact('navigation'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(PermissionRequest $request): RedirectResponse|JsonResponse
    {
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully.',
                'permission' => $permission,
            ], 201);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission): View
    {
        abort_unless(Gate::allows('permissions.edit') || Gate::allows('permissions.manage'), 403);

        $navigation = config('navigation');

        return view('permissions.edit', compact('permission', 'navigation'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(PermissionRequest $request, Permission $permission): RedirectResponse|JsonResponse
    {
        $permission->update([
            'name' => $request->name,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
                'permission' => $permission,
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Request $request, Permission $permission): RedirectResponse|JsonResponse
    {
        abort_unless(Gate::allows('permissions.delete') || Gate::allows('permissions.manage'), 403);

        $permission->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.',
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}

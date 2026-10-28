<?php

namespace App\Http\Controllers\Administrator;

use App\DataTables\ModuleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Models\Module;
use App\Models\NavigationGroup;
use App\Models\Permission;
use App\Support\Navigation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModuleController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.modules.view', only: ['index', 'show']),
            new Middleware('permission:admin.modules.manage', only: ['create', 'store', 'edit', 'update', 'destroy', 'reorder', 'toggleStatus']),
        ];
    }

    /**
     * Display a listing of modules via Yajra DataTable.
     */
    public function index(ModuleDataTable $dataTable)
    {
        $navigationGroups = NavigationGroup::query()->orderBy('sort_order')->get();
        $groups = $navigationGroups->pluck('label')
            ->merge(Module::distinct()->whereNotNull('group_label')->pluck('group_label'))
            ->unique()
            ->values();

        return $dataTable->render('administrator.modules.index', [
            'navigation' => Navigation::getTree(),
            'groups' => $groups,
            'navigationGroups' => $navigationGroups,
        ]);
    }

    /**
     * Show the form for creating a new module.
     */
    public function create(): View
    {
        $navigationGroups = NavigationGroup::query()->orderBy('sort_order')->get();
        $groups = $navigationGroups->pluck('label')
            ->merge(Module::distinct()->whereNotNull('group_label')->pluck('group_label'))
            ->unique()
            ->values();
        $parentModules = Module::whereNull('parent_id')->orderBy('group_order')->orderBy('sort_order')->get(['id', 'label', 'group_label']);
        $existingPermissions = Permission::orderBy('name')->pluck('name');

        return view('administrator.modules.create', [
            'navigation' => Navigation::getTree(),
            'groups' => $groups,
            'navigationGroups' => $navigationGroups,
            'parentModules' => $parentModules,
            'existingPermissions' => $existingPermissions,
        ]);
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(StoreModuleRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $groupLabel = trim($validated['group_label']);
        $navGroup = NavigationGroup::where('label', $groupLabel)->first();
        if ($navGroup) {
            $validated['navigation_group_id'] = $navGroup->id;
            $validated['group_order'] = $validated['group_order'] ?? $navGroup->sort_order;
        } else {
            $newGroup = NavigationGroup::create([
                'uuid' => (string) Str::uuid(),
                'label' => $groupLabel,
                'icon' => 'folder',
                'sort_order' => $validated['group_order'] ?? ((int) NavigationGroup::max('sort_order') + 10),
                'is_active' => true,
            ]);
            $validated['navigation_group_id'] = $newGroup->id;
            $validated['group_order'] = $newGroup->sort_order;
        }

        $validated['uuid'] = (string) Str::uuid();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? ((int) Module::where('group_label', $validated['group_label'])->max('sort_order') + 10);

        $module = Module::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Module '{$module->label}' created successfully.",
                'module' => $module,
            ], 201);
        }

        return redirect()->route('admin.modules.index')->with('success', "Module '{$module->label}' created successfully.");
    }

    /**
     * Display the specified module details.
     */
    public function show(Module $module): JsonResponse
    {
        $module->load(['parent', 'children', 'navigationGroup']);

        return response()->json([
            'success' => true,
            'module' => $module,
        ]);
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module): View
    {
        $navigationGroups = NavigationGroup::query()->orderBy('sort_order')->get();
        $groups = $navigationGroups->pluck('label')
            ->merge(Module::distinct()->whereNotNull('group_label')->pluck('group_label'))
            ->unique()
            ->values();
        $parentModules = Module::whereNull('parent_id')
            ->where('id', '!=', $module->id)
            ->orderBy('group_order')
            ->orderBy('sort_order')
            ->get(['id', 'label', 'group_label']);
        $existingPermissions = Permission::orderBy('name')->pluck('name');

        return view('administrator.modules.edit', [
            'navigation' => Navigation::getTree(),
            'module' => $module,
            'groups' => $groups,
            'navigationGroups' => $navigationGroups,
            'parentModules' => $parentModules,
            'existingPermissions' => $existingPermissions,
        ]);
    }

    /**
     * Update the specified module in storage.
     */
    public function update(UpdateModuleRequest $request, Module $module): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');

        $groupLabel = trim($validated['group_label']);
        $navGroup = NavigationGroup::where('label', $groupLabel)->first();
        if ($navGroup) {
            $validated['navigation_group_id'] = $navGroup->id;
            $validated['group_order'] = $validated['group_order'] ?? $navGroup->sort_order;
        } else {
            $newGroup = NavigationGroup::create([
                'uuid' => (string) Str::uuid(),
                'label' => $groupLabel,
                'icon' => 'folder',
                'sort_order' => $validated['group_order'] ?? ((int) NavigationGroup::max('sort_order') + 10),
                'is_active' => true,
            ]);
            $validated['navigation_group_id'] = $newGroup->id;
            $validated['group_order'] = $newGroup->sort_order;
        }

        $module->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Module '{$module->label}' updated successfully.",
                'module' => $module->fresh(['parent']),
            ]);
        }

        return redirect()->route('admin.modules.index')->with('success', "Module '{$module->label}' updated successfully.");
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Request $request, Module $module): JsonResponse|RedirectResponse
    {
        // Reassign children to null so they don't break
        if ($module->children()->exists()) {
            $module->children()->update(['parent_id' => null]);
        }

        $label = $module->label;
        $module->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Module '{$label}' deleted successfully.",
            ]);
        }

        return redirect()->route('admin.modules.index')->with('success', "Module '{$label}' deleted successfully.");
    }

    /**
     * Toggle the active status of the specified module.
     */
    public function toggleStatus(Request $request, Module $module): JsonResponse
    {
        $isActive = $request->has('is_active')
            ? $request->boolean('is_active')
            : ! $module->is_active;

        $module->update(['is_active' => $isActive]);

        $statusText = $isActive ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Module '{$module->label}' {$statusText} successfully.",
            'is_active' => $isActive,
        ]);
    }

    /**
     * Quick reordering of module positions.
     */
    public function reorder(Request $request): JsonResponse
    {
        // Single move: up or down
        if ($request->has('id') && $request->has('direction')) {
            $id = $request->input('id');
            $module = is_numeric($id)
                ? Module::findOrFail($id)
                : Module::where('uuid', $id)->firstOrFail();
            $direction = $request->input('direction');

            $swapModule = Module::where('group_label', $module->group_label)
                ->where('parent_id', $module->parent_id)
                ->when($direction === 'up', fn ($q) => $q->where('sort_order', '<', $module->sort_order)->orderByDesc('sort_order'))
                ->when($direction === 'down', fn ($q) => $q->where('sort_order', '>', $module->sort_order)->orderBy('sort_order'))
                ->first();

            if ($swapModule) {
                $temp = $module->sort_order;
                $module->sort_order = $swapModule->sort_order;
                $swapModule->sort_order = $temp;

                // Space them out if they had identical sort orders
                if ($module->sort_order === $swapModule->sort_order) {
                    if ($direction === 'up') {
                        $module->sort_order = max(0, $module->sort_order - 10);
                    } else {
                        $module->sort_order = $module->sort_order + 10;
                    }
                }

                $module->save();
                $swapModule->save();

                return response()->json([
                    'success' => true,
                    'message' => "Moved module '{$module->label}' {$direction}.",
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Module '{$module->label}' is already at the boundary.",
            ]);
        }

        // Batch reorder array
        if ($request->has('orders') && is_array($request->input('orders'))) {
            foreach ($request->input('orders') as $item) {
                if (isset($item['id'], $item['sort_order'])) {
                    Module::where('id', $item['id'])->update(['sort_order' => (int) $item['sort_order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Module order updated successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid reorder parameters.',
        ], 422);
    }
}

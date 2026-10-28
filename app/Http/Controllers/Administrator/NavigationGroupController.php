<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\NavigationGroup;
use App\Support\Navigation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NavigationGroupController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:admin.modules.view', only: ['index', 'show']),
            new Middleware('permission:admin.modules.manage', only: ['store', 'update', 'destroy', 'reorder']),
        ];
    }

    /**
     * Display a listing of navigation groups with their module counts.
     */
    public function index(Request $request): View|JsonResponse
    {
        $groups = NavigationGroup::query()
            ->withCount('modules')
            ->orderBy('sort_order')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'groups' => $groups,
            ]);
        }

        return view('administrator.navigation-groups.index', [
            'groups' => $groups,
            'navigation' => Navigation::getTree(),
        ]);
    }

    /**
     * Store a newly created navigation group.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100', 'unique:navigation_groups,label'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['icon'] = $validated['icon'] ?: 'folder';
        $validated['sort_order'] = $validated['sort_order'] ?? ((int) NavigationGroup::max('sort_order') + 10);
        $validated['is_active'] = $request->boolean('is_active', true);

        $group = NavigationGroup::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Navigation Group '{$group->label}' created successfully.",
                'group' => $group,
            ], 201);
        }

        return redirect()->route('admin.navigation-groups.index')->with('success', "Navigation Group '{$group->label}' created successfully.");
    }

    /**
     * Display the specified navigation group.
     */
    public function show(NavigationGroup $navigationGroup): JsonResponse
    {
        $navigationGroup->loadCount('modules');

        return response()->json([
            'success' => true,
            'group' => $navigationGroup,
        ]);
    }

    /**
     * Update the specified navigation group.
     */
    public function update(Request $request, NavigationGroup $navigationGroup): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100', 'unique:navigation_groups,label,'.$navigationGroup->id],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $oldLabel = $navigationGroup->label;
        $validated['icon'] = $validated['icon'] ?: $navigationGroup->icon;
        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : $navigationGroup->is_active;

        $navigationGroup->update($validated);

        // Synchronize modules if label or order changed
        if ($oldLabel !== $navigationGroup->label || isset($validated['sort_order'])) {
            Module::where('navigation_group_id', $navigationGroup->id)
                ->orWhere('group_label', $oldLabel)
                ->update([
                    'navigation_group_id' => $navigationGroup->id,
                    'group_label' => $navigationGroup->label,
                    'group_order' => $navigationGroup->sort_order,
                ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Navigation Group '{$navigationGroup->label}' updated successfully.",
                'group' => $navigationGroup,
            ]);
        }

        return redirect()->route('admin.navigation-groups.index')->with('success', "Navigation Group '{$navigationGroup->label}' updated successfully.");
    }

    /**
     * Remove the specified navigation group from storage.
     */
    public function destroy(Request $request, NavigationGroup $navigationGroup): RedirectResponse|JsonResponse
    {
        $moduleCount = Module::where('navigation_group_id', $navigationGroup->id)->count();

        if ($moduleCount > 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete '{$navigationGroup->label}' because it contains {$moduleCount} module(s). Please move or delete its modules first.",
                ], 422);
            }

            return redirect()->back()->with('error', "Cannot delete '{$navigationGroup->label}' because it contains {$moduleCount} module(s). Please move or delete its modules first.");
        }

        $label = $navigationGroup->label;
        $navigationGroup->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Navigation Group '{$label}' deleted successfully.",
            ]);
        }

        return redirect()->route('admin.navigation-groups.index')->with('success', "Navigation Group '{$label}' deleted successfully.");
    }

    /**
     * Reorder navigation groups.
     */
    public function reorder(Request $request): JsonResponse
    {
        if ($request->has('id') && $request->has('direction')) {
            $id = $request->input('id');
            $group = is_numeric($id)
                ? NavigationGroup::findOrFail($id)
                : NavigationGroup::where('uuid', $id)->firstOrFail();

            $direction = $request->input('direction');

            $swapGroup = NavigationGroup::query()
                ->when($direction === 'up', fn ($q) => $q->where('sort_order', '<', $group->sort_order)->orderByDesc('sort_order'))
                ->when($direction === 'down', fn ($q) => $q->where('sort_order', '>', $group->sort_order)->orderBy('sort_order'))
                ->first();

            if ($swapGroup) {
                $temp = $group->sort_order;
                $group->sort_order = $swapGroup->sort_order;
                $swapGroup->sort_order = $temp;

                if ($group->sort_order === $swapGroup->sort_order) {
                    $group->sort_order = $direction === 'up'
                        ? max(0, $group->sort_order - 10)
                        : $group->sort_order + 10;
                }

                $group->save();
                $swapGroup->save();

                Module::where('navigation_group_id', $group->id)->update(['group_order' => $group->sort_order]);
                Module::where('navigation_group_id', $swapGroup->id)->update(['group_order' => $swapGroup->sort_order]);

                return response()->json([
                    'success' => true,
                    'message' => "Moved group '{$group->label}' {$direction}.",
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Group '{$group->label}' is already at the boundary.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid reorder parameters.',
        ], 400);
    }
}

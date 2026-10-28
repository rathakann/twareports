<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\ItemGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ItemGroupController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.item-groups.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of item groups or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.item-groups.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = ItemGroup::query()->select('master_item_groups.*');

            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('itms_grp_nam', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('itms_grp_nam', 'like', "%{$keyword}%")
                            ->orWhere('itms_grp_cod', 'like', "%{$keyword}%")
                            ->orWhere('category', 'like', "%{$keyword}%")
                            ->orWhere('valuation_method', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (ItemGroup $ig): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$ig->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = ItemGroup::query()->select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.item-groups.index', compact('categories', 'statuses', 'navigation'));
    }

    /**
     * Display the specified item group details.
     */
    public function show(ItemGroup $itemGroup): JsonResponse
    {
        Gate::authorize('master-data.item-groups.view');

        return response()->json([
            'success' => true,
            'data' => $itemGroup,
        ]);
    }
}

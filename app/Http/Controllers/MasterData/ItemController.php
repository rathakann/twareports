<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.items.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of items or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.items.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Item::query()->select('master_items.*');

            if ($request->filled('item_group') && $request->item_group !== 'all') {
                $query->where('itms_grp_nam', $request->item_group);
            }

            if ($request->filled('item_class') && $request->item_class !== 'all') {
                $query->where('item_class', $request->item_class);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('item_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('item_name', 'like', "%{$keyword}%")
                            ->orWhere('item_code', 'like', "%{$keyword}%")
                            ->orWhere('foreign_name', 'like', "%{$keyword}%")
                            ->orWhere('barcode', 'like', "%{$keyword}%")
                            ->orWhere('itms_grp_nam', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Item $item): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$item->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $itemGroups = Item::query()->select('itms_grp_nam')->whereNotNull('itms_grp_nam')->distinct()->orderBy('itms_grp_nam')->pluck('itms_grp_nam');
        $itemClasses = Item::query()->select('item_class')->whereNotNull('item_class')->distinct()->orderBy('item_class')->pluck('item_class');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.items.index', compact('itemGroups', 'itemClasses', 'statuses', 'navigation'));
    }

    /**
     * Display the specified item details.
     */
    public function show(Item $item): JsonResponse
    {
        Gate::authorize('master-data.items.view');

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }
}

<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\ItemClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ItemClassController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.item-classes.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of item classes or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.item-classes.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = ItemClass::query()->select('master_item_classes.*');

            if ($request->filled('item_type') && $request->item_type !== 'all') {
                $query->where('item_type', $request->item_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('class_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('class_name', 'like', "%{$keyword}%")
                            ->orWhere('class_code', 'like', "%{$keyword}%")
                            ->orWhere('item_type', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (ItemClass $ic): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$ic->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $types = ItemClass::query()->select('item_type')->distinct()->orderBy('item_type')->pluck('item_type');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.item-classes.index', compact('types', 'statuses', 'navigation'));
    }

    /**
     * Display the specified item class details.
     */
    public function show(ItemClass $itemClass): JsonResponse
    {
        Gate::authorize('master-data.item-classes.view');

        return response()->json([
            'success' => true,
            'data' => $itemClass,
        ]);
    }
}

<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.warehouses.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of warehouses or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.warehouses.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Warehouse::query()->select('master_warehouses.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('branch_code') && $request->branch_code !== 'all') {
                $query->where('branch_code', $request->branch_code);
            }

            return DataTables::of($query)
                ->filterColumn('whs_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('whs_name', 'like', "%{$keyword}%")
                            ->orWhere('whs_code', 'like', "%{$keyword}%")
                            ->orWhere('location', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Warehouse $whs): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$whs->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $branches = Warehouse::query()->select('branch_code')->whereNotNull('branch_code')->distinct()->orderBy('branch_code')->pluck('branch_code');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.warehouses.index', compact('branches', 'statuses', 'navigation'));
    }

    /**
     * Display the specified warehouse details.
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        Gate::authorize('master-data.warehouses.view');

        return response()->json([
            'success' => true,
            'data' => $warehouse,
        ]);
    }
}

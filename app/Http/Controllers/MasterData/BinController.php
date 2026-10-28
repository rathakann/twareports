<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Bin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BinController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.bins.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of bins or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.bins.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Bin::query()->select('master_bins.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('whs_code') && $request->whs_code !== 'all') {
                $query->where('whs_code', $request->whs_code);
            }

            return DataTables::of($query)
                ->filterColumn('bin_code', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('bin_code', 'like', "%{$keyword}%")
                            ->orWhere('whs_code', 'like', "%{$keyword}%")
                            ->orWhere('aisle', 'like', "%{$keyword}%")
                            ->orWhere('barcode', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Bin $bin): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$bin->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $warehouses = Bin::query()->select('whs_code')->distinct()->orderBy('whs_code')->pluck('whs_code');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.bins.index', compact('warehouses', 'statuses', 'navigation'));
    }

    /**
     * Display the specified bin details.
     */
    public function show(Bin $bin): JsonResponse
    {
        Gate::authorize('master-data.bins.view');

        return response()->json([
            'success' => true,
            'data' => $bin,
        ]);
    }
}

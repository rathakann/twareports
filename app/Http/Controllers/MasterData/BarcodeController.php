<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Barcode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BarcodeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.barcodes.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of barcodes or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.barcodes.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Barcode::query()->select('master_barcodes.*');

            if ($request->filled('barcode_type') && $request->barcode_type !== 'all') {
                $query->where('barcode_type', $request->barcode_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('item_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('item_name', 'like', "%{$keyword}%")
                            ->orWhere('item_code', 'like', "%{$keyword}%")
                            ->orWhere('barcode', 'like', "%{$keyword}%")
                            ->orWhere('uom_code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Barcode $bc): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$bc->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $types = Barcode::query()->select('barcode_type')->distinct()->orderBy('barcode_type')->pluck('barcode_type');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.barcodes.index', compact('types', 'statuses', 'navigation'));
    }

    /**
     * Display the specified barcode details.
     */
    public function show(Barcode $barcode): JsonResponse
    {
        Gate::authorize('master-data.barcodes.view');

        return response()->json([
            'success' => true,
            'data' => $barcode,
        ]);
    }
}

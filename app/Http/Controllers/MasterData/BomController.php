<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Bom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BomController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.boms.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of BOMs or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.boms.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Bom::query()->select('master_boms.*');

            if ($request->filled('tree_type') && $request->tree_type !== 'all') {
                $query->where('tree_type', $request->tree_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('tree_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('tree_name', 'like', "%{$keyword}%")
                            ->orWhere('tree_code', 'like', "%{$keyword}%")
                            ->orWhere('tree_type', 'like', "%{$keyword}%")
                            ->orWhere('whs_code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Bom $bom): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$bom->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $types = ['Assembly', 'Production', 'Sales', 'Template'];
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.boms.index', compact('types', 'statuses', 'navigation'));
    }

    /**
     * Display the specified BOM details.
     */
    public function show(Bom $bom): JsonResponse
    {
        Gate::authorize('master-data.boms.view');

        return response()->json([
            'success' => true,
            'data' => $bom,
        ]);
    }
}

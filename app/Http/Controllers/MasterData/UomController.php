<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Uom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UomController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.uoms.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of UoMs or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.uoms.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Uom::query()->select('master_uoms.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('uom_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('uom_name', 'like', "%{$keyword}%")
                            ->orWhere('uom_code', 'like', "%{$keyword}%")
                            ->orWhere('intl_code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Uom $uom): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$uom->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.uoms.index', compact('statuses', 'navigation'));
    }

    /**
     * Display the specified UoM details.
     */
    public function show(Uom $uom): JsonResponse
    {
        Gate::authorize('master-data.uoms.view');

        return response()->json([
            'success' => true,
            'data' => $uom,
        ]);
    }
}

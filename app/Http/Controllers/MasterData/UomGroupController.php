<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\UomGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UomGroupController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.uom-groups.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of UoM Groups or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.uom-groups.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = UomGroup::query()->select('master_uom_groups.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('ugp_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('ugp_name', 'like', "%{$keyword}%")
                            ->orWhere('ugp_code', 'like', "%{$keyword}%")
                            ->orWhere('base_uom_code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (UomGroup $ug): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$ug->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.uom-groups.index', compact('statuses', 'navigation'));
    }

    /**
     * Display the specified UoM Group details.
     */
    public function show(UomGroup $uomGroup): JsonResponse
    {
        Gate::authorize('master-data.uom-groups.view');

        return response()->json([
            'success' => true,
            'data' => $uomGroup,
        ]);
    }
}

<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
// use App\Models\MasterData\Branch;
use App\Models\Sap\Branch;
use App\Models\Sap\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.branches.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of branches or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {

        Gate::authorize('master-data.branches.view');

        /* if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Branch::query()->select('master_branches.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('branch_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('branch_name', 'like', "%{$keyword}%")
                            ->orWhere('bpl_id', 'like', "%{$keyword}%")
                            ->orWhere('city', 'like', "%{$keyword}%")
                            ->orWhere('tax_id', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Branch $branch): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="' . $branch->uuid . '" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }*/
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            // "BPLId", "BPLName", "BPLFrName", "DflWhs", "TaxIdNum", "Street", "Disabled"
            $query = Branch::query()->select([
                'BPLId',
                'BPLName',
                'BPLFrName',
                'DflWhs',
                'TaxIdNum',
                'Address',
                'Street',
                'UpdateDate',
                'Disabled',
            ]);

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('Disabled', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('BPLName', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('BPLName', 'like', "%{$keyword}%")
                            ->orWhere('BPLId', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('UpdateDate', function (Branch $branch) {
                    return $branch->UpdateDate;
                })
                ->addColumn('status', function ($row) {
                    return $row->Disabled === 'N' ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function (Branch $branch): string {
                    return '<button type="button"
                        class="btn btn-sm btn-outline-primary btn-action btn-view-item"
                        data-id="'.$branch->BPLId.'"
                        title="View Details">
                        <i data-lucide="eye"></i>
                    </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.branches.index', compact('statuses', 'navigation'));
    }

    /**
     * Display the specified branch details.
     */
    public function show(mixed $branch): JsonResponse
    {
        Gate::authorize('master-data.branches.view');

        if ($branch instanceof Branch) {
            return response()->json([
                'success' => true,
                'data' => $branch,
            ]);
        }

        if (is_numeric($branch)) {
            $record = Branch::find($branch);
            if ($record) {
                return response()->json([
                    'success' => true,
                    'data' => $record,
                ]);
            }
        }

        if (class_exists(\App\Models\MasterData\Branch::class)) {
            $local = \App\Models\MasterData\Branch::where('uuid', $branch)->orWhere('id', $branch)->first();
            if ($local) {
                return response()->json([
                    'success' => true,
                    'data' => $local,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Branch not found.',
        ], 404);
    }
}

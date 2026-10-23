<?php

namespace App\Http\Controllers\Reports\Sales;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DepositReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.sales.deposits.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of deposits or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.sales.deposits.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.sales.deposits', $request->input('export_format'));
            }

            $query = Deposit::query()->select('deposits.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
                $query->where('deposit_type', $request->deposit_type);
            }

            if ($request->filled('reconciliation_status') && $request->reconciliation_status !== 'all') {
                $query->where('reconciliation_status', $request->reconciliation_status);
            }

            if ($request->filled('bank_name') && $request->bank_name !== 'all') {
                $query->where('bank_name', $request->bank_name);
            }

            if ($request->filled('created_date')) {
                $date = trim($request->created_date);
                if (str_contains($date, ' to ')) {
                    [$start, $end] = explode(' to ', $date);
                    $query->whereDate('deposit_date', '>=', trim($start))
                        ->whereDate('deposit_date', '<=', trim($end));
                } else {
                    $query->whereDate('deposit_date', $date);
                }
            }

            return DataTables::of($query)
                ->filterColumn('deposit_num', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('deposit_num', 'like', "%{$keyword}%")
                            ->orWhere('bank_name', 'like', "%{$keyword}%")
                            ->orWhere('bank_account', 'like', "%{$keyword}%")
                            ->orWhere('journal_ref', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Deposit $deposit): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-deposit" data-id="'.$deposit->id.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $banks = Deposit::query()
            ->select('bank_name')
            ->distinct()
            ->orderBy('bank_name')
            ->pluck('bank_name');

        $statuses = ['Posted', 'Draft', 'Cancelled'];
        $depositTypes = ['Cash', 'Check', 'Credit Card', 'Electronic'];
        $reconciliationStatuses = ['Reconciled', 'Unreconciled'];
        $navigation = config('navigation');

        return view('reports.sales.deposits.index', compact('banks', 'statuses', 'depositTypes', 'reconciliationStatuses', 'navigation'));
    }

    /**
     * Display the specified deposit details.
     */
    public function show(Deposit $deposit): JsonResponse
    {
        Gate::authorize('reports.sales.deposits.view');

        return response()->json([
            'success' => true,
            'data' => $deposit,
        ]);
    }
}

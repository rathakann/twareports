<?php

namespace App\Http\Controllers\Reports\Financial;

use App\Http\Controllers\Controller;
use App\Models\TrialBalanceAccount;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TrialBalanceReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.financial.trial-balance.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display the Trial Balance report or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.financial.trial-balance.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.financial.trial-balance', $request->input('export_format'));
            }

            $query = TrialBalanceAccount::query()->select('trial_balance_accounts.*');

            if ($request->filled('classification') && $request->classification !== 'all') {
                $query->where('classification', $request->classification);
            }

            if ($request->filled('statement_type') && $request->statement_type !== 'all') {
                $query->where('statement_type', $request->statement_type);
            }

            if ($request->filled('created_date')) {
                $date = trim($request->created_date);
                if (str_contains($date, ' to ')) {
                    [$start, $end] = explode(' to ', $date);
                    $query->whereDate('doc_date', '>=', trim($start))
                        ->whereDate('doc_date', '<=', trim($end));
                } else {
                    $query->whereDate('doc_date', $date);
                }
            }

            return DataTables::of($query)
                ->filterColumn('account_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('account_code', 'like', "%{$keyword}%")
                            ->orWhere('account_name', 'like', "%{$keyword}%")
                            ->orWhere('classification', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (TrialBalanceAccount $account): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-account" data-id="'.$account->uuid.'" title="View Ledger Card"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $classifications = TrialBalanceAccount::query()
            ->select('classification')
            ->distinct()
            ->orderBy('classification')
            ->pluck('classification');

        $statements = ['Balance Sheet', 'Profit & Loss'];

        $totalDebits = TrialBalanceAccount::sum('debit_amount');
        $totalCredits = TrialBalanceAccount::sum('credit_amount');
        $accountCount = TrialBalanceAccount::distinct('account_code')->count('account_code');
        $balanceDiff = abs($totalDebits - $totalCredits);

        $navigation = config('navigation');

        return view('reports.financial.trial-balance.index', compact(
            'classifications',
            'statements',
            'totalDebits',
            'totalCredits',
            'accountCount',
            'balanceDiff',
            'navigation'
        ));
    }

    /**
     * Display the specified Trial Balance account details.
     */
    public function show(TrialBalanceAccount $trialBalance): JsonResponse
    {
        Gate::authorize('reports.financial.trial-balance.view');

        return response()->json([
            'success' => true,
            'data' => $trialBalance,
        ]);
    }
}

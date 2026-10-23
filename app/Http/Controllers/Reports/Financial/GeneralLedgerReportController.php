<?php

namespace App\Http\Controllers\Reports\Financial;

use App\Http\Controllers\Controller;
use App\Models\GeneralLedgerEntry;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GeneralLedgerReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.financial.general-ledger.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display the General Ledger report or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.financial.general-ledger.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.financial.general-ledger', $request->input('export_format'));
            }

            $query = GeneralLedgerEntry::query()->select('general_ledger_entries.*');

            if ($request->filled('account_type') && $request->account_type !== 'all') {
                $query->where('account_type', $request->account_type);
            }

            if ($request->filled('source_doc_type') && $request->source_doc_type !== 'all') {
                $query->where('source_doc_type', $request->source_doc_type);
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
                ->filterColumn('description', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('entry_number', 'like', "%{$keyword}%")
                            ->orWhere('account_code', 'like', "%{$keyword}%")
                            ->orWhere('account_name', 'like', "%{$keyword}%")
                            ->orWhere('source_doc_num', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (GeneralLedgerEntry $entry): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-entry" data-id="'.$entry->id.'" title="View Journal Entry"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $accountTypes = GeneralLedgerEntry::query()
            ->select('account_type')
            ->distinct()
            ->orderBy('account_type')
            ->pluck('account_type');

        $sourceDocTypes = GeneralLedgerEntry::query()
            ->select('source_doc_type')
            ->distinct()
            ->orderBy('source_doc_type')
            ->pluck('source_doc_type');

        $totalEntries = GeneralLedgerEntry::count();
        $totalDebit = GeneralLedgerEntry::sum('debit');
        $totalCredit = GeneralLedgerEntry::sum('credit');
        $netMovement = abs($totalDebit - $totalCredit);

        $navigation = config('navigation');

        return view('reports.financial.general-ledger.index', compact(
            'accountTypes',
            'sourceDocTypes',
            'totalEntries',
            'totalDebit',
            'totalCredit',
            'netMovement',
            'navigation'
        ));
    }

    /**
     * Display the specified General Ledger entry details.
     */
    public function show(GeneralLedgerEntry $generalLedger): JsonResponse
    {
        Gate::authorize('reports.financial.general-ledger.view');

        return response()->json([
            'success' => true,
            'data' => $generalLedger,
        ]);
    }
}

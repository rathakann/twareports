<?php

namespace App\Http\Controllers\Reports\Financial;

use App\Http\Controllers\Controller;
use App\Models\CashFlowEntry;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CashFlowReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.financial.cash-flow.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display the Cash Flow Statement report or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.financial.cash-flow.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.financial.cash-flow', $request->input('export_format'));
            }

            $query = CashFlowEntry::query()->select('cash_flow_entries.*');

            if ($request->filled('activity_type') && $request->activity_type !== 'all') {
                $query->where('activity_type', $request->activity_type);
            }

            if ($request->filled('flow_type') && $request->flow_type !== 'all') {
                $query->where('flow_type', $request->flow_type);
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
                        $sub->where('reference_num', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%")
                            ->orWhere('account_name', 'like', "%{$keyword}%")
                            ->orWhere('activity_type', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (CashFlowEntry $entry): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-entry" data-id="'.$entry->uuid.'" title="View Cash Entry Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $activityTypes = CashFlowEntry::query()
            ->select('activity_type')
            ->distinct()
            ->orderBy('activity_type')
            ->pluck('activity_type');

        $netOperating = CashFlowEntry::where('activity_type', 'Operating Activities')->sum('net_amount');
        $netInvesting = CashFlowEntry::where('activity_type', 'Investing Activities')->sum('net_amount');
        $netFinancing = CashFlowEntry::where('activity_type', 'Financing Activities')->sum('net_amount');
        $netCashChange = CashFlowEntry::sum('net_amount');

        $navigation = config('navigation');

        return view('reports.financial.cash-flow.index', compact(
            'activityTypes',
            'netOperating',
            'netInvesting',
            'netFinancing',
            'netCashChange',
            'navigation'
        ));
    }

    /**
     * Display the specified Cash Flow entry details.
     */
    public function show(CashFlowEntry $cashFlow): JsonResponse
    {
        Gate::authorize('reports.financial.cash-flow.view');

        return response()->json([
            'success' => true,
            'data' => $cashFlow,
        ]);
    }
}

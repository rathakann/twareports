<?php

namespace App\Http\Controllers\Reports\Financial;

use App\Http\Controllers\Controller;
use App\Models\BalanceSheetItem;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BalanceSheetReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.financial.balance-sheet.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display the Balance Sheet report or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.financial.balance-sheet.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.financial.balance-sheet', $request->input('export_format'));
            }

            $query = BalanceSheetItem::query()->select('balance_sheet_items.*');

            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('created_date')) {
                $date = trim($request->created_date);
                if (str_contains($date, ' to ')) {
                    [$start, $end] = explode(' to ', $date);
                    $query->whereDate('as_of_date', '>=', trim($start))
                        ->whereDate('as_of_date', '<=', trim($end));
                } else {
                    $query->whereDate('as_of_date', $date);
                }
            }

            return DataTables::of($query)
                ->filterColumn('account_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('account_code', 'like', "%{$keyword}%")
                            ->orWhere('account_name', 'like', "%{$keyword}%")
                            ->orWhere('category', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (BalanceSheetItem $item): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$item->uuid.'" title="View Account Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = BalanceSheetItem::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $totalAssets = BalanceSheetItem::whereIn('category', ['Current Assets', 'Non-Current Assets'])->sum('amount');
        $totalLiabilities = BalanceSheetItem::whereIn('category', ['Current Liabilities', 'Long-Term Liabilities'])->sum('amount');
        $totalEquity = BalanceSheetItem::where('category', 'Equity')->sum('amount');
        $workingCapital = BalanceSheetItem::where('category', 'Current Assets')->sum('amount') - BalanceSheetItem::where('category', 'Current Liabilities')->sum('amount');

        $navigation = config('navigation');

        return view('reports.financial.balance-sheet.index', compact(
            'categories',
            'totalAssets',
            'totalLiabilities',
            'totalEquity',
            'workingCapital',
            'navigation'
        ));
    }

    /**
     * Display the specified Balance Sheet item details.
     */
    public function show(BalanceSheetItem $balanceSheet): JsonResponse
    {
        Gate::authorize('reports.financial.balance-sheet.view');

        return response()->json([
            'success' => true,
            'data' => $balanceSheet,
        ]);
    }
}

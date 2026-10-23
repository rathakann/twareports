<?php

namespace App\Http\Controllers\Reports\Financial;

use App\Http\Controllers\Controller;
use App\Models\ProfitLossItem;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProfitLossReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.financial.profit-loss.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display the Profit & Loss report or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.financial.profit-loss.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.financial.profit-loss', $request->input('export_format'));
            }

            $query = ProfitLossItem::query()->select('profit_loss_items.*');

            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->filled('department') && $request->department !== 'all') {
                $query->where('department', $request->department);
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
                            ->orWhere('category', 'like', "%{$keyword}%")
                            ->orWhere('department', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('variance', function (ProfitLossItem $item): string {
                    $prev = (float) $item->previous_period_amount;
                    if ($prev == 0) {
                        return '+0.0%';
                    }
                    $pct = (($item->amount - $prev) / $prev) * 100;

                    return ($pct >= 0 ? '+' : '').number_format($pct, 1).'%';
                })
                ->addColumn('action', function (ProfitLossItem $item): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$item->id.'" title="View Line Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = ProfitLossItem::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $departments = ProfitLossItem::query()
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $totalRevenue = ProfitLossItem::where('category', 'Operating Revenue')->sum('amount');
        $totalCogs = ProfitLossItem::where('category', 'Cost of Goods Sold')->sum('amount');
        $grossProfit = $totalRevenue - $totalCogs;
        $totalOpex = ProfitLossItem::where('category', 'Operating Expense')->sum('amount');
        $netProfit = $grossProfit - $totalOpex - ProfitLossItem::where('category', 'Tax Expense')->sum('amount');

        $navigation = config('navigation');

        return view('reports.financial.profit-loss.index', compact(
            'categories',
            'departments',
            'totalRevenue',
            'grossProfit',
            'totalOpex',
            'netProfit',
            'navigation'
        ));
    }

    /**
     * Display the specified P&L item details.
     */
    public function show(ProfitLossItem $profitLoss): JsonResponse
    {
        Gate::authorize('reports.financial.profit-loss.view');

        return response()->json([
            'success' => true,
            'data' => $profitLoss,
        ]);
    }
}

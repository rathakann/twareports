<?php

namespace App\Http\Controllers\Reports\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesPurchaseOrder;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SalesPurchaseOrderReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.sales.purchase-orders.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of sales purchase orders or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.sales.purchase-orders.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.sales.purchase-orders', $request->input('export_format'));
            }

            $query = SalesPurchaseOrder::query()->select('sales_purchase_orders.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('customer') && $request->customer !== 'all') {
                $query->where('customer_code', $request->customer);
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
                ->filterColumn('doc_num', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('doc_num', 'like', "%{$keyword}%")
                            ->orWhere('customer_name', 'like', "%{$keyword}%")
                            ->orWhere('customer_code', 'like', "%{$keyword}%")
                            ->orWhere('customer_ref_num', 'like', "%{$keyword}%")
                            ->orWhere('sales_employee', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (SalesPurchaseOrder $order): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-order" data-id="'.$order->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = SalesPurchaseOrder::query()
            ->select('customer_code', 'customer_name')
            ->distinct()
            ->orderBy('customer_name')
            ->get();

        $statuses = ['Open', 'Closed', 'Pending', 'Cancelled'];
        $navigation = config('navigation');

        return view('reports.sales.purchase-orders.index', compact('customers', 'statuses', 'navigation'));
    }

    /**
     * Display the specified sales purchase order details.
     */
    public function show(SalesPurchaseOrder $purchaseOrder): JsonResponse
    {
        Gate::authorize('reports.sales.purchase-orders.view');

        return response()->json([
            'success' => true,
            'data' => $purchaseOrder,
        ]);
    }
}

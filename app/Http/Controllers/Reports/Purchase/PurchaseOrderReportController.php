<?php

namespace App\Http\Controllers\Reports\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.purchase.purchase-orders.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of purchase orders or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.purchase.purchase-orders.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.purchase.purchase-orders', $request->input('export_format'));
            }

            $query = PurchaseOrder::query()->select('purchase_orders.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('vendor') && $request->vendor !== 'all') {
                $query->where('vendor_code', $request->vendor);
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
                            ->orWhere('vendor_name', 'like', "%{$keyword}%")
                            ->orWhere('vendor_code', 'like', "%{$keyword}%")
                            ->orWhere('buyer_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (PurchaseOrder $po): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-po" data-id="'.$po->id.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $vendors = PurchaseOrder::query()
            ->select('vendor_code', 'vendor_name')
            ->distinct()
            ->orderBy('vendor_name')
            ->get();

        $statuses = ['Open', 'Closed', 'Pending', 'Cancelled'];
        $navigation = config('navigation');

        return view('reports.purchase.purchase-orders.index', compact('vendors', 'statuses', 'navigation'));
    }

    /**
     * Display the specified purchase order details.
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        Gate::authorize('reports.purchase.purchase-orders.view');

        return response()->json([
            'success' => true,
            'data' => $purchaseOrder,
        ]);
    }
}

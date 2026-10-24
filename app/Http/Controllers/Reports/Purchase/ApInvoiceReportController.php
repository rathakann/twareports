<?php

namespace App\Http\Controllers\Reports\Purchase;

use App\Http\Controllers\Controller;
use App\Models\ApInvoice;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ApInvoiceReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.purchase.ap-invoices.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of AP invoices or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.purchase.ap-invoices.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.purchase.ap-invoices', $request->input('export_format'));
            }

            $query = ApInvoice::query()->select('ap_invoices.*');

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
                ->filterColumn('invoice_num', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('invoice_num', 'like', "%{$keyword}%")
                            ->orWhere('po_reference', 'like', "%{$keyword}%")
                            ->orWhere('vendor_name', 'like', "%{$keyword}%")
                            ->orWhere('vendor_code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (ApInvoice $invoice): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-invoice" data-id="'.$invoice->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $vendors = ApInvoice::query()
            ->select('vendor_code', 'vendor_name')
            ->distinct()
            ->orderBy('vendor_name')
            ->get();

        $statuses = ['Unpaid', 'Paid', 'Partially Paid', 'Overdue', 'Cancelled'];
        $navigation = config('navigation');

        return view('reports.purchase.ap-invoices.index', compact('vendors', 'statuses', 'navigation'));
    }

    /**
     * Display the specified AP invoice details.
     */
    public function show(ApInvoice $apInvoice): JsonResponse
    {
        Gate::authorize('reports.purchase.ap-invoices.view');

        return response()->json([
            'success' => true,
            'data' => $apInvoice,
        ]);
    }
}

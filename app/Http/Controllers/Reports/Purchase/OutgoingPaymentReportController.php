<?php

namespace App\Http\Controllers\Reports\Purchase;

use App\Http\Controllers\Controller;
use App\Models\OutgoingPayment;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OutgoingPaymentReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.purchase.outgoing-payments.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of outgoing payments or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.purchase.outgoing-payments.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.purchase.outgoing-payments', $request->input('export_format'));
            }

            $query = OutgoingPayment::query()->select('outgoing_payments.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('vendor') && $request->vendor !== 'all') {
                $query->where('vendor_code', $request->vendor);
            }

            if ($request->filled('payment_method') && $request->payment_method !== 'all') {
                $query->where('payment_method', $request->payment_method);
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
                ->filterColumn('payment_num', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('payment_num', 'like', "%{$keyword}%")
                            ->orWhere('vendor_name', 'like', "%{$keyword}%")
                            ->orWhere('vendor_code', 'like', "%{$keyword}%")
                            ->orWhere('reference_num', 'like', "%{$keyword}%")
                            ->orWhere('bank_account', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (OutgoingPayment $payment): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-payment" data-id="'.$payment->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $vendors = OutgoingPayment::query()
            ->select('vendor_code', 'vendor_name')
            ->distinct()
            ->orderBy('vendor_name')
            ->get();

        $statuses = ['Completed', 'Pending', 'Cancelled'];
        $paymentMethods = ['Bank Transfer', 'Check', 'Cash', 'Credit Card'];
        $navigation = config('navigation');

        return view('reports.purchase.outgoing-payments.index', compact('vendors', 'statuses', 'paymentMethods', 'navigation'));
    }

    /**
     * Display the specified outgoing payment details.
     */
    public function show(OutgoingPayment $outgoingPayment): JsonResponse
    {
        Gate::authorize('reports.purchase.outgoing-payments.view');

        return response()->json([
            'success' => true,
            'data' => $outgoingPayment,
        ]);
    }
}

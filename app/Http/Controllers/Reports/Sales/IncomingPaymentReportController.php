<?php

namespace App\Http\Controllers\Reports\Sales;

use App\Http\Controllers\Controller;
use App\Models\IncomingPayment;
use App\Support\ExportPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class IncomingPaymentReportController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.sales.incoming-payments.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of incoming payments or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('reports.sales.incoming-payments.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            if ($request->boolean('is_export') || $request->input('length') == -1) {
                ExportPermission::authorize('reports.sales.incoming-payments', $request->input('export_format'));
            }

            $query = IncomingPayment::query()->select('incoming_payments.*');

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('customer') && $request->customer !== 'all') {
                $query->where('customer_code', $request->customer);
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
                            ->orWhere('customer_name', 'like', "%{$keyword}%")
                            ->orWhere('customer_code', 'like', "%{$keyword}%")
                            ->orWhere('reference_num', 'like', "%{$keyword}%")
                            ->orWhere('bank_account', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (IncomingPayment $payment): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-payment" data-id="'.$payment->id.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = IncomingPayment::query()
            ->select('customer_code', 'customer_name')
            ->distinct()
            ->orderBy('customer_name')
            ->get();

        $statuses = ['Cleared', 'Pending', 'Bounced', 'Cancelled'];
        $paymentMethods = ['Bank Transfer', 'Check', 'Cash', 'Credit Card'];
        $navigation = config('navigation');

        return view('reports.sales.incoming-payments.index', compact('customers', 'statuses', 'paymentMethods', 'navigation'));
    }

    /**
     * Display the specified incoming payment details.
     */
    public function show(IncomingPayment $incomingPayment): JsonResponse
    {
        Gate::authorize('reports.sales.incoming-payments.view');

        return response()->json([
            'success' => true,
            'data' => $incomingPayment,
        ]);
    }
}

<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\BusinessPartner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BusinessPartnerController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.business-partners.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of business partners or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.business-partners.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = BusinessPartner::query()->select('master_business_partners.*');

            if ($request->filled('card_type') && $request->card_type !== 'all') {
                $query->where('card_type', $request->card_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('card_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('card_name', 'like', "%{$keyword}%")
                            ->orWhere('card_code', 'like', "%{$keyword}%")
                            ->orWhere('group_name', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (BusinessPartner $bp): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$bp->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $types = ['Customer', 'Vendor', 'Lead'];
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.business-partners.index', compact('types', 'statuses', 'navigation'));
    }

    /**
     * Display the specified business partner details.
     */
    public function show(BusinessPartner $businessPartner): JsonResponse
    {
        Gate::authorize('master-data.business-partners.view');

        return response()->json([
            'success' => true,
            'data' => $businessPartner,
        ]);
    }
}

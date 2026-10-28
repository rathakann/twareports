<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\DocumentSeries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DocumentSeriesController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.document-series.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of document series or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.document-series.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = DocumentSeries::query()->select('master_document_series.*');

            if ($request->filled('doc_name') && $request->doc_name !== 'all') {
                $query->where('doc_name', $request->doc_name);
            }

            if ($request->filled('period_indicator') && $request->period_indicator !== 'all') {
                $query->where('period_indicator', $request->period_indicator);
            }

            if ($request->filled('branch_code') && $request->branch_code !== 'all') {
                $query->where('branch_code', $request->branch_code);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('series_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('series_name', 'like', "%{$keyword}%")
                            ->orWhere('series_code', 'like', "%{$keyword}%")
                            ->orWhere('doc_name', 'like', "%{$keyword}%")
                            ->orWhere('object_code', 'like', "%{$keyword}%")
                            ->orWhere('prefix', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (DocumentSeries $ds): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$ds->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $docTypes = DocumentSeries::query()->select('doc_name')->distinct()->orderBy('doc_name')->pluck('doc_name');
        $periods = DocumentSeries::query()->whereNotNull('period_indicator')->distinct()->orderByDesc('period_indicator')->pluck('period_indicator');
        $branches = DocumentSeries::query()->whereNotNull('branch_code')->distinct()->orderBy('branch_code')->pluck('branch_code');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.document-series.index', compact('docTypes', 'periods', 'branches', 'statuses', 'navigation'));
    }

    /**
     * Display the specified document series details.
     */
    public function show(DocumentSeries $documentSeries): JsonResponse
    {
        Gate::authorize('master-data.document-series.view');

        return response()->json([
            'success' => true,
            'data' => $documentSeries,
        ]);
    }
}

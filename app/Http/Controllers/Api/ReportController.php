<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Display a listing of reports for the authenticated user.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Report::class);

        $reports = $request->user()->is_admin
            ? Report::with('user')->latest()->paginate(15)
            : $request->user()->reports()->latest()->paginate(15);

        return ReportResource::collection($reports);
    }

    /**
     * Store a newly created report.
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        /** @var Report $report */
        $report = $request->user()->reports()->create($request->validated());

        return response()->json([
            'report' => ReportResource::make($report),
        ], 201);
    }

    /**
     * Display the specified report after authorizing ownership or admin rights.
     */
    public function show(Report $report): JsonResponse
    {
        Gate::authorize('view', $report);

        return response()->json([
            'report' => ReportResource::make($report->load('user')),
        ]);
    }

    /**
     * Update the specified report after validating ownership.
     */
    public function update(UpdateReportRequest $request, Report $report): JsonResponse
    {
        Gate::authorize('update', $report);

        $report->update($request->validated());

        return response()->json([
            'report' => ReportResource::make($report->load('user')),
        ]);
    }

    /**
     * Remove the specified report after authorizing permissions.
     */
    public function destroy(Report $report): JsonResponse
    {
        Gate::authorize('delete', $report);

        $report->delete();

        return response()->json([
            'message' => 'Report deleted successfully.',
        ]);
    }
}

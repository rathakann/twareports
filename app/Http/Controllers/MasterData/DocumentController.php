<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:master-data.documents.view', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of document types or return DataTables JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('master-data.documents.view');

        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $query = Document::query()->select('master_documents.*');

            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->filterColumn('doc_name', function ($q, $keyword): void {
                    $q->where(function ($sub) use ($keyword): void {
                        $sub->where('doc_name', 'like', "%{$keyword}%")
                            ->orWhere('object_code', 'like', "%{$keyword}%")
                            ->orWhere('sap_table_name', 'like', "%{$keyword}%")
                            ->orWhere('category', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function (Document $doc): string {
                    return '<button type="button" class="btn btn-sm btn-outline-primary btn-action btn-view-item" data-id="'.$doc->uuid.'" title="View Details"><i data-lucide="eye"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = Document::query()->select('category')->distinct()->orderBy('category')->pluck('category');
        $statuses = ['Active', 'Inactive'];
        $navigation = config('navigation');

        return view('master-data.documents.index', compact('categories', 'statuses', 'navigation'));
    }

    /**
     * Display the specified document details.
     */
    public function show(Document $document): JsonResponse
    {
        Gate::authorize('master-data.documents.view');

        return response()->json([
            'success' => true,
            'data' => $document,
        ]);
    }
}

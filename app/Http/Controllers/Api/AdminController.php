<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    /**
     * Provide administrative overview and metrics for authorized administrators only.
     */
    public function metrics(Request $request): JsonResponse
    {
        Gate::authorize('viewAnyAdmin', Report::class);

        return response()->json([
            'total_users' => User::count(),
            'total_reports' => Report::count(),
            'admin_access' => true,
        ]);
    }
}

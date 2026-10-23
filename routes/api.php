<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware(['auth:sanctum', 'ability:issue-access-token', 'throttle:refresh']);

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');

    Route::get('/me', [AuthController::class, 'me'])
        ->middleware(['auth:sanctum', 'ability:access-api']);
});

Route::middleware(['auth:sanctum', 'ability:access-api'])->group(function (): void {
    Route::get('/user', function (Request $request) {
        return UserResource::make($request->user());
    });

    Route::apiResource('reports', ReportController::class);

    Route::prefix('admin')->group(function (): void {
        Route::get('/metrics', [AdminController::class, 'metrics']);
    });
});

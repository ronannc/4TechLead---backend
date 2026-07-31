<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DailyMeetingController;
use App\Http\Controllers\Api\V1\DailyMeetingEntryController;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('people', PersonController::class);
    // Daily history is append-only: only index/show/store are routed (see DailyMeetingPolicy /
    // DailyMeetingEntryPolicy, which also deny update/delete at the authorization layer).
    Route::apiResource('daily-meetings', DailyMeetingController::class)->only(['index', 'show', 'store']);
    Route::apiResource('daily-meeting-entries', DailyMeetingEntryController::class)->only(['index', 'show']);
});

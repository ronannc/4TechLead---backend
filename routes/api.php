<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DailyMeetingController;
use App\Http\Controllers\Api\V1\DailyMeetingEntryController;
use App\Http\Controllers\Api\V1\DevelopmentPlanController;
use App\Http\Controllers\Api\V1\DevelopmentPlanItemController;
use App\Http\Controllers\Api\V1\IntegrationSystemController;
use App\Http\Controllers\Api\V1\IntegrationWebhookController;
use App\Http\Controllers\Api\V1\OkrController;
use App\Http\Controllers\Api\V1\OkrKeyResultController;
use App\Http\Controllers\Api\V1\OneOnOneSessionController;
use App\Http\Controllers\Api\V1\OneOnOneTemplateController;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\PersonDeliveryMetricController;
use App\Http\Controllers\Api\V1\PersonExternalIdentityController;
use App\Http\Controllers\Api\V1\PersonGrowthSuggestionController;
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

Route::post('integration-webhooks/{integrationSystem}', IntegrationWebhookController::class);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('people', PersonController::class);
    Route::apiResource('integration-systems', IntegrationSystemController::class);
    Route::apiResource('person-external-identities', PersonExternalIdentityController::class);
    Route::apiResource('person-delivery-metrics', PersonDeliveryMetricController::class)->only(['index', 'show']);
    Route::get('people/{person}/growth-suggestions', PersonGrowthSuggestionController::class);
    Route::apiResource('one-on-one-templates', OneOnOneTemplateController::class);
    Route::apiResource('one-on-one-sessions', OneOnOneSessionController::class);
    Route::apiResource('development-plans', DevelopmentPlanController::class);
    Route::apiResource('development-plan-items', DevelopmentPlanItemController::class);
    Route::apiResource('okrs', OkrController::class);
    Route::apiResource('okr-key-results', OkrKeyResultController::class);
    // Daily history is append-only: only index/show/store are routed (see DailyMeetingPolicy /
    // DailyMeetingEntryPolicy, which also deny update/delete at the authorization layer).
    Route::apiResource('daily-meetings', DailyMeetingController::class)->only(['index', 'show', 'store']);
    Route::apiResource('daily-meeting-entries', DailyMeetingEntryController::class)->only(['index', 'show']);
});

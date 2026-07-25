<?php

use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

Route::apiResource('teams', TeamController::class);
Route::apiResource('people', PersonController::class);

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\DevelopmentPlan\IndexDevelopmentPlanRequest;
use App\Http\Requests\DevelopmentPlan\StoreDevelopmentPlanRequest;
use App\Http\Requests\DevelopmentPlan\UpdateDevelopmentPlanRequest;
use App\Http\Resources\DevelopmentPlanResource;
use App\Models\DevelopmentPlan;

final class DevelopmentPlanController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = DevelopmentPlan::class;
        $this->resource = DevelopmentPlanResource::class;
        $this->storeRequest = StoreDevelopmentPlanRequest::class;
        $this->updateRequest = UpdateDevelopmentPlanRequest::class;
        $this->indexRequest = IndexDevelopmentPlanRequest::class;
    }
}

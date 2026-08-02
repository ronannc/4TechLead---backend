<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\DevelopmentPlanItem\IndexDevelopmentPlanItemRequest;
use App\Http\Requests\DevelopmentPlanItem\StoreDevelopmentPlanItemRequest;
use App\Http\Requests\DevelopmentPlanItem\UpdateDevelopmentPlanItemRequest;
use App\Http\Resources\DevelopmentPlanItemResource;
use App\Models\DevelopmentPlanItem;

final class DevelopmentPlanItemController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = DevelopmentPlanItem::class;
        $this->resource = DevelopmentPlanItemResource::class;
        $this->storeRequest = StoreDevelopmentPlanItemRequest::class;
        $this->updateRequest = UpdateDevelopmentPlanItemRequest::class;
        $this->indexRequest = IndexDevelopmentPlanItemRequest::class;
    }
}

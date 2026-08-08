<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonDeliveryMetric\IndexPersonDeliveryMetricRequest;
use App\Http\Resources\PersonDeliveryMetricResource;
use App\Models\PersonDeliveryMetric;

final class PersonDeliveryMetricController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = PersonDeliveryMetric::class;
        $this->resource = PersonDeliveryMetricResource::class;
        $this->indexRequest = IndexPersonDeliveryMetricRequest::class;
    }
}

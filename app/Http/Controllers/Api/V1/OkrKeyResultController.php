<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\OkrKeyResult\IndexOkrKeyResultRequest;
use App\Http\Requests\OkrKeyResult\StoreOkrKeyResultRequest;
use App\Http\Requests\OkrKeyResult\UpdateOkrKeyResultRequest;
use App\Http\Resources\OkrKeyResultResource;
use App\Models\OkrKeyResult;

final class OkrKeyResultController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = OkrKeyResult::class;
        $this->resource = OkrKeyResultResource::class;
        $this->storeRequest = StoreOkrKeyResultRequest::class;
        $this->updateRequest = UpdateOkrKeyResultRequest::class;
        $this->indexRequest = IndexOkrKeyResultRequest::class;
    }
}

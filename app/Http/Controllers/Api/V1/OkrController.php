<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Okr\IndexOkrRequest;
use App\Http\Requests\Okr\StoreOkrRequest;
use App\Http\Requests\Okr\UpdateOkrRequest;
use App\Http\Resources\OkrResource;
use App\Models\Okr;

final class OkrController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = Okr::class;
        $this->resource = OkrResource::class;
        $this->storeRequest = StoreOkrRequest::class;
        $this->updateRequest = UpdateOkrRequest::class;
        $this->indexRequest = IndexOkrRequest::class;
    }
}

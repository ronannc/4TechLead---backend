<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\OneOnOneSession\IndexOneOnOneSessionRequest;
use App\Http\Requests\OneOnOneSession\StoreOneOnOneSessionRequest;
use App\Http\Requests\OneOnOneSession\UpdateOneOnOneSessionRequest;
use App\Http\Resources\OneOnOneSessionResource;
use App\Models\OneOnOneSession;

final class OneOnOneSessionController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = OneOnOneSession::class;
        $this->resource = OneOnOneSessionResource::class;
        $this->storeRequest = StoreOneOnOneSessionRequest::class;
        $this->updateRequest = UpdateOneOnOneSessionRequest::class;
        $this->indexRequest = IndexOneOnOneSessionRequest::class;
    }
}

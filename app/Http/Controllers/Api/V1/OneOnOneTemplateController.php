<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\OneOnOneTemplate\IndexOneOnOneTemplateRequest;
use App\Http\Requests\OneOnOneTemplate\StoreOneOnOneTemplateRequest;
use App\Http\Requests\OneOnOneTemplate\UpdateOneOnOneTemplateRequest;
use App\Http\Resources\OneOnOneTemplateResource;
use App\Models\OneOnOneTemplate;

final class OneOnOneTemplateController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = OneOnOneTemplate::class;
        $this->resource = OneOnOneTemplateResource::class;
        $this->storeRequest = StoreOneOnOneTemplateRequest::class;
        $this->updateRequest = UpdateOneOnOneTemplateRequest::class;
        $this->indexRequest = IndexOneOnOneTemplateRequest::class;
    }
}

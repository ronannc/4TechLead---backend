<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\IntegrationSystem\IndexIntegrationSystemRequest;
use App\Http\Requests\IntegrationSystem\StoreIntegrationSystemRequest;
use App\Http\Requests\IntegrationSystem\UpdateIntegrationSystemRequest;
use App\Http\Resources\IntegrationSystemResource;
use App\Models\IntegrationSystem;
use App\Services\IntegrationSystemStoreService;

final class IntegrationSystemController extends Controller
{
    use CrudControllerTrait;

    public function __construct(IntegrationSystemStoreService $storeService)
    {
        $this->model = IntegrationSystem::class;
        $this->resource = IntegrationSystemResource::class;
        $this->storeRequest = StoreIntegrationSystemRequest::class;
        $this->updateRequest = UpdateIntegrationSystemRequest::class;
        $this->indexRequest = IndexIntegrationSystemRequest::class;
        $this->storeService = $storeService;
    }
}

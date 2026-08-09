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
use App\Services\IntegrationSystemTokenService;
use Illuminate\Http\JsonResponse;
use Throwable;

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

    /**
     * @throws Throwable
     */
    public function regenerateToken(
        IntegrationSystem $integrationSystem,
        IntegrationSystemTokenService $tokenService,
    ): JsonResponse {
        $this->authorize('update', $integrationSystem);

        $integrationSystem = $tokenService->regenerate($integrationSystem);

        return (new IntegrationSystemResource($integrationSystem))->response();
    }
}

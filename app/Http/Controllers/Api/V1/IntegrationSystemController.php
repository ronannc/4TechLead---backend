<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\IntegrationSystem\IndexIntegrationSystemRequest;
use App\Http\Requests\IntegrationSystem\StoreIntegrationSystemRequest;
use App\Http\Requests\IntegrationSystem\UpdateIntegrationSystemRequest;
use App\Http\Resources\IntegrationSystemResource;
use App\Models\IntegrationSystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

final class IntegrationSystemController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = IntegrationSystem::class;
        $this->resource = IntegrationSystemResource::class;
        $this->storeRequest = StoreIntegrationSystemRequest::class;
        $this->updateRequest = UpdateIntegrationSystemRequest::class;
        $this->indexRequest = IndexIntegrationSystemRequest::class;
    }

    public function store(): JsonResponse
    {
        $this->authorize('create', IntegrationSystem::class);

        $token = Str::random(64);
        $data = $this->validateWith($this->storeRequest);
        $integration = IntegrationSystem::query()->create([
            ...$data,
            'active' => $data['active'] ?? true,
            'token_hash' => hash('sha256', $token),
            'token_prefix' => substr($token, 0, 8),
        ]);
        $integration->setAttribute('webhook_token', $token);

        return (new IntegrationSystemResource($integration))->response()->setStatusCode(201);
    }
}

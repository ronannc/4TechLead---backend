<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IntegrationWebhook\StoreIntegrationWebhookRequest;
use App\Http\Resources\IntegrationWebhookEventResource;
use App\Models\IntegrationSystem;
use App\Services\IntegrationWebhookIngestService;
use Illuminate\Http\JsonResponse;

final class IntegrationWebhookController extends Controller
{
    public function __invoke(
        StoreIntegrationWebhookRequest $request,
        IntegrationSystem $integrationSystem,
        IntegrationWebhookIngestService $service,
    ): JsonResponse {
        $event = $service->ingest(
            integrationSystem: $integrationSystem,
            token: $request->bearerToken() ?? (string) $request->header('X-Integration-Token'),
            data: $request->validated(),
        );

        return (new IntegrationWebhookEventResource($event))->response()->setStatusCode(200);
    }
}

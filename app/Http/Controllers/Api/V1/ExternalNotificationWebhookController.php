<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalNotification\StoreExternalNotificationWebhookRequest;
use App\Http\Resources\ExternalNotificationResource;
use App\Models\IntegrationSystem;
use App\Services\ExternalNotificationIngestService;
use Illuminate\Http\JsonResponse;

final class ExternalNotificationWebhookController extends Controller
{
    public function __invoke(
        StoreExternalNotificationWebhookRequest $request,
        IntegrationSystem $integrationSystem,
        ExternalNotificationIngestService $service,
    ): JsonResponse {
        $notification = $service->ingest(
            integrationSystem: $integrationSystem,
            token: $request->bearerToken() ?? (string) $request->header('X-Integration-Token'),
            data: $request->validated(),
        );

        return (new ExternalNotificationResource($notification->load('integrationSystem')))
            ->response()
            ->setStatusCode(200);
    }
}

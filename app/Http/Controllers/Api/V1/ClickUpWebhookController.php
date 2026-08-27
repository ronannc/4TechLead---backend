<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IntegrationWebhookEventResource;
use App\Services\ClickUpWebhookIngestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ClickUpWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        ClickUpWebhookIngestService $service,
    ): JsonResponse {
        $event = $service->ingest(
            $this->integrationToken($request),
            $request->all(),
        );

        return (new IntegrationWebhookEventResource($event))
            ->response()
            ->setStatusCode(200);
    }

    protected function integrationToken(Request $request): string
    {
        $token = $request->bearerToken();

        if (is_string($token) && $token !== '') {
            return $token;
        }

        $token = (string) $request->header('X-Integration-Token', '');

        if ($token !== '') {
            return $token;
        }

        return (string) $request->query('token', '');
    }
}

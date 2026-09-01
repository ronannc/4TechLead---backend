<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IntegrationWebhookEventResource;
use App\Services\GitHubWebhookIngestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GitHubWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        GitHubWebhookIngestService $service,
    ): JsonResponse {
        $headers = [
            'delivery' => $request->header('X-GitHub-Delivery'),
            'event' => $request->header('X-GitHub-Event'),
            'hook_id' => $request->header('X-GitHub-Hook-ID'),
            'signature_256' => $request->header('X-Hub-Signature-256'),
            'user_agent' => $request->header('User-Agent'),
        ];

        $token = $this->integrationToken($request);
        $event = $token === ''
            ? $service->ingestSigned(
                payload: $request->all(),
                rawBody: $request->getContent(),
                headers: $headers,
            )
            : $service->ingest(
                token: $token,
                payload: $request->all(),
                rawBody: $request->getContent(),
                headers: $headers,
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

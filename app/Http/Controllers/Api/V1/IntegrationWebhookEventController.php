<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ListParams;
use App\Http\Controllers\Controller;
use App\Http\Requests\IntegrationWebhook\IndexIntegrationWebhookEventRequest;
use App\Http\Resources\IntegrationWebhookEventResource;
use App\Models\IntegrationWebhookEvent;
use App\Services\IntegrationWebhookEventIndexService;
use Illuminate\Http\JsonResponse;

final class IntegrationWebhookEventController extends Controller
{
    public function __construct(
        private readonly IntegrationWebhookEventIndexService $indexService,
    ) {}

    public function index(IndexIntegrationWebhookEventRequest $request): JsonResponse
    {
        $this->authorize('viewAny', IntegrationWebhookEvent::class);

        return IntegrationWebhookEventResource::collection(
            $this->indexService->index(ListParams::fromRequest($request))
        )->response();
    }

    public function show(IntegrationWebhookEvent $integrationWebhookEvent): JsonResponse
    {
        $this->authorize('view', $integrationWebhookEvent);

        return (new IntegrationWebhookEventResource(
            $integrationWebhookEvent->load(['integrationSystem', 'person', 'deliveryMetrics'])
        ))->response();
    }

    public function destroy(IntegrationWebhookEvent $integrationWebhookEvent): JsonResponse
    {
        $this->authorize('delete', $integrationWebhookEvent);

        $integrationWebhookEvent->delete();

        return response()->json(status: 204);
    }
}

<?php

namespace App\Services;

use App\Contracts\Services\IndexServiceContract;
use App\DTOs\ListParams;
use App\Models\IntegrationWebhookEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class IntegrationWebhookEventIndexService implements IndexServiceContract
{
    public function index(ListParams $params): LengthAwarePaginator
    {
        return IntegrationWebhookEvent::query()
            ->with(['integrationSystem', 'person'])
            ->withCount('deliveryMetrics')
            ->filter($params->filters)
            ->operationalFilters($params->filters)
            ->search($params->search)
            ->operationalOrder($params->order)
            ->paginate(perPage: $params->perPage, page: $params->page);
    }
}

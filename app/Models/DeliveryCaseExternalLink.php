<?php

namespace App\Models;

use App\Enums\DeliveryExternalLinkType;
use App\Models\Concerns\BelongsToTenant;
use Database\Factories\DeliveryCaseExternalLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'delivery_case_id',
    'integration_system_id',
    'integration_webhook_event_id',
    'link_type',
    'external_id',
    'source_ref',
    'metadata',
])]
class DeliveryCaseExternalLink extends Model
{
    /** @use HasFactory<DeliveryCaseExternalLinkFactory> */
    use BelongsToTenant, HasFactory;

    /**
     * @return BelongsTo<DeliveryCase, $this>
     */
    public function deliveryCase(): BelongsTo
    {
        return $this->belongsTo(DeliveryCase::class);
    }

    /**
     * @return BelongsTo<IntegrationSystem, $this>
     */
    public function integrationSystem(): BelongsTo
    {
        return $this->belongsTo(IntegrationSystem::class);
    }

    /**
     * @return BelongsTo<IntegrationWebhookEvent, $this>
     */
    public function integrationWebhookEvent(): BelongsTo
    {
        return $this->belongsTo(IntegrationWebhookEvent::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'link_type' => DeliveryExternalLinkType::class,
            'metadata' => 'array',
        ];
    }
}

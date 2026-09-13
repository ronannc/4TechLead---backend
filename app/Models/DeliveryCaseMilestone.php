<?php

namespace App\Models;

use App\Enums\DeliveryMilestoneType;
use App\Models\Concerns\BelongsToTenant;
use Database\Factories\DeliveryCaseMilestoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'delivery_case_id',
    'integration_webhook_event_id',
    'milestone_type',
    'semantic_key',
    'source_provider',
    'source_ref',
    'occurred_at',
    'metadata',
])]
class DeliveryCaseMilestone extends Model
{
    /** @use HasFactory<DeliveryCaseMilestoneFactory> */
    use BelongsToTenant, HasFactory;

    /**
     * @return BelongsTo<DeliveryCase, $this>
     */
    public function deliveryCase(): BelongsTo
    {
        return $this->belongsTo(DeliveryCase::class);
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
            'milestone_type' => DeliveryMilestoneType::class,
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}

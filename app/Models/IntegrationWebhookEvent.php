<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\IntegrationWebhookEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'integration_system_id',
    'tenant_id',
    'person_id',
    'event_id',
    'event_type',
    'external_actor_code',
    'status',
    'failure_reason',
    'payload',
    'normalized_payload',
    'received_at',
])]
class IntegrationWebhookEvent extends Model
{
    /** @use HasFactory<IntegrationWebhookEventFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return BelongsTo<IntegrationSystem, $this>
     */
    public function integrationSystem(): BelongsTo
    {
        return $this->belongsTo(IntegrationSystem::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return HasMany<PersonDeliveryMetric, $this>
     */
    public function deliveryMetrics(): HasMany
    {
        return $this->hasMany(PersonDeliveryMetric::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'normalized_payload' => 'array',
            'received_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['integration_system_id', 'person_id', 'event_type', 'status', 'external_actor_code'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['event_id', 'event_type', 'external_actor_code', 'failure_reason'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['received_at', 'created_at', 'updated_at'];
    }
}

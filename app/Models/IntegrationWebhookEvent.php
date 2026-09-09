<?php

namespace App\Models;

use App\Enums\SortDirection;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\IntegrationWebhookEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    'payload_hash',
    'payload_size_bytes',
    'normalized_payload',
    'received_at',
])]
class IntegrationWebhookEvent extends Model
{
    /** @use HasFactory<IntegrationWebhookEventFactory> */
    use BelongsToTenant, Filterable, HasFactory, SoftDeletes;

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
            'payload_size_bytes' => 'integer',
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

    /**
     * @param  Builder<IntegrationWebhookEvent>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<IntegrationWebhookEvent>
     */
    public function scopeOperationalFilters(Builder $query, array $filters): Builder
    {
        if ((bool) ($filters['unmapped'] ?? false)) {
            $query->whereNull('person_id');
        }

        if ((bool) ($filters['with_failure'] ?? false)) {
            $query->whereNotNull('failure_reason');
        }

        if (isset($filters['received_from'])) {
            $query->where('received_at', '>=', $filters['received_from']);
        }

        if (isset($filters['received_to'])) {
            $query->where('received_at', '<=', $filters['received_to']);
        }

        return $query;
    }

    /**
     * @param  Builder<IntegrationWebhookEvent>  $query
     * @param  array<string, string>  $order
     * @return Builder<IntegrationWebhookEvent>
     */
    public function scopeOperationalOrder(Builder $query, array $order): Builder
    {
        $applied = false;

        foreach ($order as $field => $direction) {
            if (! in_array($field, $this->sortableFields(), true)) {
                continue;
            }

            $query->orderBy($field, SortDirection::tryFrom((string) $direction)?->value ?? SortDirection::Descending->value);
            $applied = true;
        }

        return $applied ? $query : $query->orderByDesc('received_at')->orderByDesc('id');
    }
}

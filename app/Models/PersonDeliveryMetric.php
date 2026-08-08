<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\PersonDeliveryMetricFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'integration_system_id',
    'integration_webhook_event_id',
    'metric_type',
    'metric_value',
    'unit',
    'source_ref',
    'period_start',
    'period_end',
    'occurred_at',
    'metadata',
])]
class PersonDeliveryMetric extends Model
{
    /** @use HasFactory<PersonDeliveryMetricFactory> */
    use Filterable, HasFactory;

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
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
            'metric_value' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['person_id', 'integration_system_id', 'integration_webhook_event_id', 'metric_type'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['metric_type', 'unit', 'source_ref'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['occurred_at', 'created_at', 'updated_at', 'metric_value'];
    }
}

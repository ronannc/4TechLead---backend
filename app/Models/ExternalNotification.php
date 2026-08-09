<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\ExternalNotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'integration_system_id',
    'event_id',
    'title',
    'message',
    'type',
    'severity',
    'source_ref',
    'payload',
    'metadata',
    'received_at',
])]
class ExternalNotification extends Model
{
    /** @use HasFactory<ExternalNotificationFactory> */
    use Filterable, HasFactory;

    /**
     * @return BelongsTo<IntegrationSystem, $this>
     */
    public function integrationSystem(): BelongsTo
    {
        return $this->belongsTo(IntegrationSystem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'metadata' => 'array',
            'received_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['integration_system_id', 'type', 'severity'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'message', 'type', 'severity', 'source_ref'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['received_at', 'created_at', 'updated_at', 'severity', 'type'];
    }
}

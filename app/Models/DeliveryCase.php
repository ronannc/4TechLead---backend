<?php

namespace App\Models;

use App\Enums\DeliveryStage;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\DeliveryCaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tenant_id',
    'task_ref',
    'title',
    'current_stage',
    'sprint_ref',
    'story_points',
    'first_seen_at',
    'last_activity_at',
    'completed_at',
    'canceled_at',
])]
class DeliveryCase extends Model
{
    /** @use HasFactory<DeliveryCaseFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return HasMany<DeliveryCaseParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(DeliveryCaseParticipant::class);
    }

    /**
     * @return HasMany<DeliveryCaseMilestone, $this>
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(DeliveryCaseMilestone::class)
            ->oldest('occurred_at')
            ->oldest('id');
    }

    /**
     * @return HasMany<DeliveryCaseExternalLink, $this>
     */
    public function externalLinks(): HasMany
    {
        return $this->hasMany(DeliveryCaseExternalLink::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_stage' => DeliveryStage::class,
            'story_points' => 'decimal:2',
            'first_seen_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'completed_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['task_ref', 'current_stage', 'sprint_ref'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['task_ref', 'title', 'sprint_ref'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['task_ref', 'last_activity_at', 'completed_at', 'created_at', 'updated_at'];
    }
}

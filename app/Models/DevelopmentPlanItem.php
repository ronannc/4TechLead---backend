<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\DevelopmentPlanItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'development_plan_id',
    'title',
    'description',
    'competency',
    'evidence',
    'status',
    'due_date',
    'completed_at',
    'progress',
])]
class DevelopmentPlanItem extends Model
{
    /** @use HasFactory<DevelopmentPlanItemFactory> */
    use Filterable, HasFactory;

    /**
     * @return BelongsTo<DevelopmentPlan, $this>
     */
    public function developmentPlan(): BelongsTo
    {
        return $this->belongsTo(DevelopmentPlan::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'date',
            'progress' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['development_plan_id', 'status', 'competency'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'description', 'competency', 'evidence'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['due_date', 'completed_at', 'created_at', 'updated_at', 'progress'];
    }
}

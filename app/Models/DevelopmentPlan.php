<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\DevelopmentPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'person_id',
    'tenant_id',
    'title',
    'summary',
    'status',
    'start_date',
    'end_date',
    'target_role',
    'target_seniority',
    'progress',
])]
class DevelopmentPlan extends Model
{
    /** @use HasFactory<DevelopmentPlanFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    protected $with = ['items'];

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return HasMany<DevelopmentPlanItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(DevelopmentPlanItem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'progress' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['person_id', 'status', 'target_seniority'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'summary', 'target_role'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['start_date', 'end_date', 'created_at', 'updated_at', 'progress'];
    }
}

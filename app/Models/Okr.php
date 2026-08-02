<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\OkrFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'person_id',
    'development_plan_id',
    'objective',
    'cycle',
    'status',
    'focus_area',
    'diagnosis',
    'evidence_source',
    'baseline',
    'target',
    'start_date',
    'end_date',
    'confidence',
    'progress',
])]
class Okr extends Model
{
    /** @use HasFactory<OkrFactory> */
    use Filterable, HasFactory;

    protected $with = ['keyResults'];

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo<DevelopmentPlan, $this>
     */
    public function developmentPlan(): BelongsTo
    {
        return $this->belongsTo(DevelopmentPlan::class);
    }

    /**
     * @return HasMany<OkrKeyResult, $this>
     */
    public function keyResults(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'confidence' => 'integer',
            'progress' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['person_id', 'development_plan_id', 'status', 'cycle', 'focus_area'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['objective', 'diagnosis', 'evidence_source', 'baseline', 'target'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['start_date', 'end_date', 'created_at', 'updated_at', 'confidence', 'progress'];
    }
}

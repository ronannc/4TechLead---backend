<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\OkrKeyResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'okr_id',
    'title',
    'metric_name',
    'initial_value',
    'current_value',
    'target_value',
    'unit',
    'confidence',
    'status',
    'due_date',
    'evidence',
    'progress',
])]
class OkrKeyResult extends Model
{
    /** @use HasFactory<OkrKeyResultFactory> */
    use Filterable, HasFactory;

    /**
     * @return BelongsTo<Okr, $this>
     */
    public function okr(): BelongsTo
    {
        return $this->belongsTo(Okr::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_value' => 'decimal:2',
            'current_value' => 'decimal:2',
            'target_value' => 'decimal:2',
            'due_date' => 'date',
            'confidence' => 'integer',
            'progress' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['okr_id', 'status', 'metric_name'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'metric_name', 'unit', 'evidence'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['due_date', 'created_at', 'updated_at', 'confidence', 'progress'];
    }
}

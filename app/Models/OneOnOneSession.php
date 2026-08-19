<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\OneOnOneSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'tenant_id',
    'one_on_one_template_id',
    'scheduled_for',
    'held_at',
    'title',
    'status',
    'sentiment',
    'questions',
    'answers',
    'notes',
    'action_items',
])]
class OneOnOneSession extends Model
{
    /** @use HasFactory<OneOnOneSessionFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo<OneOnOneTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(OneOnOneTemplate::class, 'one_on_one_template_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_for' => 'date',
            'held_at' => 'date',
            'questions' => 'array',
            'answers' => 'array',
            'action_items' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['person_id', 'one_on_one_template_id', 'status', 'sentiment'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'notes'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['scheduled_for', 'held_at', 'created_at', 'updated_at'];
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\PersonOneOnOneNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tenant_id',
    'person_id',
    'created_by',
    'one_on_one_session_id',
    'title',
    'body',
    'status',
    'occurred_at',
])]
class PersonOneOnOneNote extends Model
{
    /** @use HasFactory<PersonOneOnOneNoteFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<OneOnOneSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(OneOnOneSession::class, 'one_on_one_session_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'date',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['person_id', 'status', 'one_on_one_session_id'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'body'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['occurred_at', 'created_at', 'updated_at'];
    }
}

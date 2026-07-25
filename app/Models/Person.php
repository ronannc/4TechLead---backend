<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'team_id'])]
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use Filterable, HasFactory;

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['team_id'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['name'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['name', 'created_at'];
    }
}

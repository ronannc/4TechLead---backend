<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'tenant_id'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return HasMany<Person, $this>
     */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['name'];
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

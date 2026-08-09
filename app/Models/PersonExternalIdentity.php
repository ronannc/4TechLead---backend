<?php

namespace App\Models;

use App\Models\Concerns\Filterable;
use Database\Factories\PersonExternalIdentityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'person_id',
    'integration_system_id',
    'external_code',
    'metadata',
    'active',
])]
class PersonExternalIdentity extends Model
{
    /** @use HasFactory<PersonExternalIdentityFactory> */
    use Filterable, HasFactory;

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

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
            'metadata' => 'array',
            'active' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['person_id', 'integration_system_id', 'external_code', 'active'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['external_code'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['created_at', 'updated_at', 'external_code'];
    }
}

<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use App\Models\Concerns\Filterable;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'team_id',
    'birth_date',
    'position',
    'contract_type',
    'email',
    'admission_date',
    'seniority',
])]
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
     * @return HasMany<OneOnOneSession, $this>
     */
    public function oneOnOneSessions(): HasMany
    {
        return $this->hasMany(OneOnOneSession::class);
    }

    /**
     * @return HasMany<DevelopmentPlan, $this>
     */
    public function developmentPlans(): HasMany
    {
        return $this->hasMany(DevelopmentPlan::class);
    }

    /**
     * @return HasMany<PersonExternalIdentity, $this>
     */
    public function externalIdentities(): HasMany
    {
        return $this->hasMany(PersonExternalIdentity::class);
    }

    /**
     * @return HasMany<PersonDeliveryMetric, $this>
     */
    public function deliveryMetrics(): HasMany
    {
        return $this->hasMany(PersonDeliveryMetric::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'admission_date' => 'date',
            'contract_type' => ContractType::class,
            'seniority' => SeniorityLevel::class,
        ];
    }

    /**
     * Derived from `birth_date` — never stored, always computed on read.
     *
     * @return Attribute<int|null, never>
     */
    protected function age(): Attribute
    {
        return Attribute::make(get: fn (): ?int => $this->birth_date?->age);
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['team_id', 'contract_type', 'seniority'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['name', 'position'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['name', 'created_at', 'birth_date', 'admission_date'];
    }
}

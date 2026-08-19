<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\OneOnOneTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'tenant_id',
    'description',
    'questions',
    'is_default',
    'active',
])]
class OneOnOneTemplate extends Model
{
    /** @use HasFactory<OneOnOneTemplateFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return HasMany<OneOnOneSession, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(OneOnOneSession::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['active', 'is_default'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['title', 'description'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['title', 'created_at', 'updated_at'];
    }
}

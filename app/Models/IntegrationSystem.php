<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\Filterable;
use Database\Factories\IntegrationSystemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'tenant_id',
    'provider',
    'description',
    'token_hash',
    'webhook_secret',
    'token_prefix',
    'active',
    'last_received_at',
])]
class IntegrationSystem extends Model
{
    /** @use HasFactory<IntegrationSystemFactory> */
    use BelongsToTenant, Filterable, HasFactory;

    /**
     * @return HasMany<PersonExternalIdentity, $this>
     */
    public function externalIdentities(): HasMany
    {
        return $this->hasMany(PersonExternalIdentity::class);
    }

    /**
     * @return HasMany<IntegrationWebhookEvent, $this>
     */
    public function webhookEvents(): HasMany
    {
        return $this->hasMany(IntegrationWebhookEvent::class);
    }

    /**
     * @return HasMany<PersonDeliveryMetric, $this>
     */
    public function deliveryMetrics(): HasMany
    {
        return $this->hasMany(PersonDeliveryMetric::class);
    }

    /**
     * @return HasMany<ExternalNotification, $this>
     */
    public function externalNotifications(): HasMany
    {
        return $this->hasMany(ExternalNotification::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'webhook_secret' => 'encrypted',
            'active' => 'boolean',
            'last_received_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function filterableFields(): array
    {
        return ['provider', 'active'];
    }

    /**
     * @return array<int, string>
     */
    protected function searchableFields(): array
    {
        return ['name', 'provider', 'description'];
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return ['name', 'provider', 'created_at', 'updated_at', 'last_received_at'];
    }
}

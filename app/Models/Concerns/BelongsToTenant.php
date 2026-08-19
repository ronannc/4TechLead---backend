<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenantId = auth()->user()?->tenant_id;

            if ($tenantId !== null) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
            }
        });

        static::creating(function (object $model): void {
            $user = auth()->user();

            if ($user instanceof User && $user->tenant_id !== null && empty($model->tenant_id)) {
                $model->tenant_id = $user->tenant_id;
            }
        });
    }
}

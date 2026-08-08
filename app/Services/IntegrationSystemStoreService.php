<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use App\Models\IntegrationSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class IntegrationSystemStoreService implements StoreServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes): IntegrationSystem {
            $token = Str::random(64);

            $integration = IntegrationSystem::query()->create([
                ...$attributes,
                'active' => $attributes['active'] ?? true,
                'token_hash' => hash('sha256', $token),
                'token_prefix' => substr($token, 0, 8),
            ]);
            $integration->setAttribute('webhook_token', $token);

            return $integration;
        });
    }
}

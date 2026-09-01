<?php

namespace App\Services;

use App\Models\IntegrationSystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class IntegrationSystemTokenService
{
    /**
     * @throws Throwable
     */
    public function regenerate(IntegrationSystem $integrationSystem): IntegrationSystem
    {
        return DB::transaction(function () use ($integrationSystem): IntegrationSystem {
            $token = Str::random(64);

            $integrationSystem->forceFill([
                'token_hash' => hash('sha256', $token),
                'webhook_secret' => $token,
                'token_prefix' => substr($token, 0, 8),
            ])->save();

            $integrationSystem->refresh();
            $integrationSystem->setAttribute('webhook_token', $token);

            return $integrationSystem;
        });
    }
}

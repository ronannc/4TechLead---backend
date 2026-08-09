<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use App\Models\PersonExternalIdentity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class PersonExternalIdentityStoreService implements StoreServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes): PersonExternalIdentity {
            return PersonExternalIdentity::query()->create([
                ...$attributes,
                'external_code' => $this->generateExternalCode(),
                'active' => $attributes['active'] ?? true,
            ]);
        });
    }

    private function generateExternalCode(): string
    {
        do {
            $code = 'ext_'.Str::lower(Str::random(20));
        } while (PersonExternalIdentity::query()->where('external_code', $code)->exists());

        return $code;
    }
}

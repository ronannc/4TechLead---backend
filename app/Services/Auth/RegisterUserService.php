<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegisterUserService
{
    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     * @return array{user: User, token: string}
     *
     * @throws Throwable
     */
    public function register(array $attributes): array
    {
        return DB::transaction(function () use ($attributes): array {
            $tenant = Tenant::query()->create([
                'name' => $attributes['name'],
            ]);

            $attributes['role'] = UserRole::TechLead;
            $attributes['tenant_id'] = $tenant->id;

            $user = User::query()->create($attributes);

            return [
                'user' => $user,
                'token' => $user->createToken('api')->plainTextToken,
            ];
        });
    }
}

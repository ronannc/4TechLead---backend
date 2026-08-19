<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Models\PersonInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AcceptPersonInvitationService
{
    /**
     * @param  array{email: string, token: string, password: string}  $attributes
     * @return array{user: User, token: string}
     *
     * @throws Throwable
     */
    public function accept(array $attributes): array
    {
        $email = mb_strtolower($attributes['email']);
        $token = $this->normalizeToken($attributes['token']);

        $invitation = PersonInvitation::query()
            ->where('email', $email)
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $invitation) {
            throw ValidationException::withMessages([
                'token' => 'Convite inválido ou expirado.',
            ]);
        }

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser !== null && $existingUser->person_id !== null && $existingUser->person_id !== $invitation->person_id) {
            throw ValidationException::withMessages([
                'email' => 'Esse usuário já está vinculado a outra pessoa.',
            ]);
        }

        $invitedTenantId = $invitation->tenant_id ?? $invitation->person?->tenant_id;

        if ($existingUser !== null && $existingUser->tenant_id !== null && $invitedTenantId !== null && $existingUser->tenant_id !== $invitedTenantId) {
            throw ValidationException::withMessages([
                'email' => 'Esse usuário já pertence a outra conta.',
            ]);
        }

        if ($existingUser !== null && ! Hash::check($attributes['password'], $existingUser->password)) {
            throw ValidationException::withMessages([
                'password' => 'A senha informada não confere com o usuário existente.',
            ]);
        }

        $linkedUser = User::query()->where('person_id', $invitation->person_id)->first();

        if ($linkedUser !== null && ($existingUser === null || $linkedUser->id !== $existingUser->id)) {
            throw ValidationException::withMessages([
                'email' => 'Essa pessoa já possui um login vinculado.',
            ]);
        }

        return DB::transaction(function () use ($attributes, $email, $existingUser, $invitation): array {
            $person = $invitation->person;

            if ($existingUser === null) {
                $user = User::query()->create([
                    'name' => $person->name,
                    'email' => $email,
                    'password' => $attributes['password'],
                    'role' => UserRole::Member,
                    'person_id' => $person->id,
                    'tenant_id' => $person->tenant_id,
                ]);
            } else {
                $existingUser->update([
                    'person_id' => $person->id,
                    'tenant_id' => $person->tenant_id,
                ]);
                $user = $existingUser;
            }

            $invitation->update(['accepted_at' => now()]);

            return [
                'user' => $user,
                'token' => $user->createToken('api')->plainTextToken,
            ];
        });
    }

    private function normalizeToken(string $token): string
    {
        return mb_strtoupper((string) preg_replace('/[\s-]+/', '', $token));
    }
}

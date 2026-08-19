<?php

namespace App\Services;

use App\Models\Person;
use App\Models\PersonInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PersonInvitationCreateService
{
    private const TokenLength = 6;

    private const TokenAlphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * @return array{invitation: PersonInvitation, token: string}
     *
     * @throws Throwable
     */
    public function create(Person $person, User $inviter, int $expiresInDays = 7): array
    {
        if ($person->email === null) {
            throw ValidationException::withMessages([
                'email' => 'Cadastre um e-mail para a pessoa antes de criar o convite.',
            ]);
        }

        if (User::query()->where('person_id', $person->id)->exists()) {
            throw ValidationException::withMessages([
                'person_id' => 'Essa pessoa já possui um login vinculado.',
            ]);
        }

        $email = mb_strtolower($person->email);

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser !== null && $existingUser->person_id !== null && $existingUser->person_id !== $person->id) {
            throw ValidationException::withMessages([
                'email' => 'Já existe um usuário com esse e-mail vinculado a outra pessoa.',
            ]);
        }

        if ($existingUser !== null && $existingUser->tenant_id !== null && $person->tenant_id !== null && $existingUser->tenant_id !== $person->tenant_id) {
            throw ValidationException::withMessages([
                'email' => 'Já existe um usuário com esse e-mail em outra conta.',
            ]);
        }

        return DB::transaction(function () use ($person, $inviter, $expiresInDays, $email): array {
            PersonInvitation::query()
                ->where('person_id', $person->id)
                ->whereNull('accepted_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            $token = $this->generateToken();

            $invitation = PersonInvitation::query()->create([
                'person_id' => $person->id,
                'tenant_id' => $person->tenant_id,
                'invited_by_user_id' => $inviter->id,
                'email' => $email,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDays($expiresInDays),
            ]);

            return [
                'invitation' => $invitation,
                'token' => $token,
            ];
        });
    }

    private function generateToken(): string
    {
        do {
            $token = '';
            $lastIndex = strlen(self::TokenAlphabet) - 1;

            for ($i = 0; $i < self::TokenLength; $i++) {
                $token .= self::TokenAlphabet[random_int(0, $lastIndex)];
            }
        } while (
            PersonInvitation::query()
                ->where('token_hash', hash('sha256', $token))
                ->whereNull('accepted_at')
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->exists()
        );

        return $token;
    }
}

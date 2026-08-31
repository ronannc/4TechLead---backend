<?php

use App\Enums\UserRole;
use App\Models\DevelopmentPlan;
use App\Models\OneOnOneSession;
use App\Models\Person;
use App\Models\PersonInvitation;
use App\Models\User;

it('registers a new tech lead user and returns a token', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Ada Lovelace')
        ->assertJsonPath('data.email', 'ada@example.com')
        ->assertJsonPath('data.role', 'tech_lead')
        ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role', 'person_id', 'created_at'], 'token']);

    expect(User::query()->where('email', 'ada@example.com')->firstOrFail()->role)->toBe(UserRole::TechLead);
});

it('rejects registration with a duplicate email', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('rejects registration when password confirmation does not match', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'something-else',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('password');
});

it('logs in with correct credentials and returns a token', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])
        ->assertOk()
        ->assertJsonPath('data.email', $user->email)
        ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role', 'person_id', 'created_at'], 'token']);
});

it('rejects login with a wrong password using a generic message', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('message', 'These credentials do not match our records.');
});

it('rejects login for a non-existent email using the same generic message', function () {
    $this->postJson('/api/v1/auth/login', [
        'email' => 'nobody@example.com',
        'password' => 'password123',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('message', 'These credentials do not match our records.');
});

it('returns the authenticated user via /auth/me', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

it('rejects /auth/me without authentication', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('logs out and revokes the current token', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $token = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->json('token');

    expect($user->tokens()->count())->toBe(1);

    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});

it('creates a manual invitation token for a person with a registered email', function () {
    $techLead = User::factory()->create();
    $person = Person::factory()->create(['email' => 'member@example.com']);

    $token = $this->actingAs($techLead, 'sanctum')
        ->postJson("/api/v1/people/{$person->id}/invitation")
        ->assertCreated()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.email', 'member@example.com')
        ->assertJsonStructure(['data' => ['id', 'person_id', 'email', 'expires_at'], 'token'])
        ->json('token');

    expect(PersonInvitation::query()->where('person_id', $person->id)->count())->toBe(1);
    expect(PersonInvitation::query()->firstOrFail()->token_hash)->not->toBeEmpty();
    expect($token)->toHaveLength(6)
        ->and($token)->toMatch('/^[A-HJ-NP-Z2-9]{6}$/');
});

it('rejects creating a person invitation without a person email', function () {
    $techLead = User::factory()->create();
    $person = Person::factory()->create(['email' => null]);

    $this->actingAs($techLead, 'sanctum')
        ->postJson("/api/v1/people/{$person->id}/invitation")
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('accepts a person invitation with email and token and creates a member login', function () {
    $techLead = User::factory()->create();
    $person = Person::factory()->create([
        'name' => 'Grace Hopper',
        'email' => 'grace@example.com',
    ]);

    $token = $this->actingAs($techLead, 'sanctum')
        ->postJson("/api/v1/people/{$person->id}/invitation")
        ->json('token');

    $typedToken = strtolower(substr($token, 0, 3).'-'.substr($token, 3));

    $this->postJson('/api/v1/auth/accept-person-invitation', [
        'email' => 'grace@example.com',
        'token' => $typedToken,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Grace Hopper')
        ->assertJsonPath('data.email', 'grace@example.com')
        ->assertJsonPath('data.role', 'member')
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonStructure(['token']);

    $member = User::query()->where('email', 'grace@example.com')->firstOrFail();

    expect($member->role)->toBe(UserRole::Member);
    expect($member->person_id)->toBe($person->id);
    expect(PersonInvitation::query()->firstOrFail()->accepted_at)->not->toBeNull();
});

it('links an existing tech lead user to the invited person without changing their role', function () {
    $techLead = User::factory()->create([
        'email' => 'lead@example.com',
        'password' => 'password123',
        'role' => UserRole::TechLead,
        'person_id' => null,
    ]);
    $person = Person::factory()->create([
        'name' => 'Tech Lead Pessoa',
        'email' => 'lead@example.com',
    ]);

    $token = $this->actingAs($techLead, 'sanctum')
        ->postJson("/api/v1/people/{$person->id}/invitation")
        ->json('token');

    $this->postJson('/api/v1/auth/accept-person-invitation', [
        'email' => 'lead@example.com',
        'token' => $token,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertCreated()
        ->assertJsonPath('data.id', $techLead->id)
        ->assertJsonPath('data.role', 'tech_lead')
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonStructure(['token']);

    expect($techLead->refresh()->role)->toBe(UserRole::TechLead)
        ->and($techLead->person_id)->toBe($person->id)
        ->and(PersonInvitation::query()->firstOrFail()->accepted_at)->not->toBeNull();
});

it('rejects linking an existing user when their password is wrong', function () {
    $techLead = User::factory()->create();
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
        'password' => 'password123',
        'person_id' => null,
    ]);
    $person = Person::factory()->create(['email' => 'existing@example.com']);

    $token = $this->actingAs($techLead, 'sanctum')
        ->postJson("/api/v1/people/{$person->id}/invitation")
        ->json('token');

    $this->postJson('/api/v1/auth/accept-person-invitation', [
        'email' => 'existing@example.com',
        'token' => $token,
        'password' => 'wrong-password',
        'password_confirmation' => 'wrong-password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('password');

    expect($existingUser->refresh()->person_id)->toBeNull();
});

it('rejects accepting a person invitation with the wrong token', function () {
    $techLead = User::factory()->create();
    $person = Person::factory()->create(['email' => 'wrong-token@example.com']);

    $this->actingAs($techLead, 'sanctum')
        ->postJson("/api/v1/people/{$person->id}/invitation")
        ->assertCreated();

    $this->postJson('/api/v1/auth/accept-person-invitation', [
        'email' => 'wrong-token@example.com',
        'token' => 'not-the-token',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('token');
});

it('lets a member read only their own person and development plans through me endpoints', function () {
    $person = Person::factory()->create(['email' => 'member-pdi@example.com']);
    $member = User::factory()->member()->create([
        'email' => 'member-pdi@example.com',
        'person_id' => $person->id,
    ]);
    $plan = DevelopmentPlan::factory()->create([
        'person_id' => $person->id,
        'title' => 'PDI comunicação técnica',
    ]);
    DevelopmentPlan::factory()->create(['title' => 'PDI de outra pessoa']);
    $ownSession = OneOnOneSession::factory()->create([
        'person_id' => $person->id,
        'title' => '1:1 próprio',
    ]);
    OneOnOneSession::factory()->create(['title' => '1:1 de outra pessoa']);

    $this->actingAs($member, 'sanctum')
        ->getJson('/api/v1/me/person')
        ->assertOk()
        ->assertJsonPath('data.id', $person->id);

    $this->actingAs($member, 'sanctum')
        ->getJson('/api/v1/me/development-plans')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $plan->id)
        ->assertJsonPath('data.0.title', 'PDI comunicação técnica');

    $this->actingAs($member, 'sanctum')
        ->getJson('/api/v1/one-on-one-sessions')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $ownSession->id)
        ->assertJsonPath('data.0.title', '1:1 próprio');

    $this->actingAs($member, 'sanctum')
        ->putJson("/api/v1/one-on-one-sessions/{$ownSession->id}", ['title' => 'Tentativa'])
        ->assertForbidden();

    $this->actingAs($member, 'sanctum')
        ->deleteJson("/api/v1/one-on-one-sessions/{$ownSession->id}")
        ->assertForbidden();

    $this->actingAs($member, 'sanctum')
        ->putJson("/api/v1/development-plans/{$plan->id}", ['title' => 'Tentativa'])
        ->assertForbidden();
});

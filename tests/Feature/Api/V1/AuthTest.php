<?php

use App\Models\User;

it('registers a new user and returns a token', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Ada Lovelace')
        ->assertJsonPath('data.email', 'ada@example.com')
        ->assertJsonStructure(['data' => ['id', 'name', 'email', 'created_at'], 'token']);

    expect(User::query()->where('email', 'ada@example.com')->exists())->toBeTrue();
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
        ->assertJsonStructure(['data' => ['id', 'name', 'email', 'created_at'], 'token']);
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

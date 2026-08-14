<?php

use App\Models\Person;
use App\Models\Team;
use App\Models\User;

it('lists teams with pagination', function () {
    Team::factory()->count(3)->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/teams')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('rejects listing teams without authentication', function () {
    $this->getJson('/api/v1/teams')->assertUnauthorized();
});

it('searches teams by name', function () {
    Team::factory()->create(['name' => 'Engineering']);
    Team::factory()->create(['name' => 'Sales']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/teams?search=Engineering')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Engineering');
});

it('orders teams by multiple columns', function () {
    Team::factory()->create(['name' => 'Beta']);
    Team::factory()->create(['name' => 'Alpha']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/teams?order[name]=asc&order[created_at]=desc')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Alpha')
        ->assertJsonPath('data.1.name', 'Beta');
});

it('shows a team', function () {
    $team = Team::factory()->create(['name' => 'Engineering']);
    Person::factory()->create(['team_id' => $team->id, 'name' => 'Ada Lovelace']);
    Person::factory()->create(['team_id' => $team->id, 'name' => 'Grace Hopper']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/teams/{$team->id}?people_page=1&people_per_page=1&people_search=Ada")
        ->assertOk()
        ->assertJsonPath('data.id', $team->id)
        ->assertJsonPath('data.name', 'Engineering')
        ->assertJsonPath('data.people.0.name', 'Ada Lovelace')
        ->assertJsonPath('data.people_meta.current_page', 1)
        ->assertJsonPath('data.people_meta.last_page', 1)
        ->assertJsonPath('data.people_meta.per_page', 1)
        ->assertJsonPath('data.people_meta.total', 1);
});

it('returns 404 for a non-existent team', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/teams/999999')
        ->assertNotFound();
});

it('creates a team', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/teams', ['name' => 'Engineering'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Engineering');

    expect(Team::query()->where('name', 'Engineering')->exists())->toBeTrue();
});

it('validates required fields when creating a team', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/teams', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('updates a team', function () {
    $team = Team::factory()->create(['name' => 'Old Name']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/teams/{$team->id}", ['name' => 'New Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name');

    expect($team->refresh()->name)->toBe('New Name');
});

it('returns 404 when updating a non-existent team', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson('/api/v1/teams/999999', ['name' => 'New Name'])
        ->assertNotFound();
});

it('deletes a team', function () {
    $team = Team::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->deleteJson("/api/v1/teams/{$team->id}")
        ->assertNoContent();

    expect(Team::query()->find($team->id))->toBeNull();
});

it('returns 404 when deleting a non-existent team', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->deleteJson('/api/v1/teams/999999')
        ->assertNotFound();
});

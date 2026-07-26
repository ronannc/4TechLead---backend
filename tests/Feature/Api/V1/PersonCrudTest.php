<?php

use App\Models\Person;
use App\Models\Team;
use App\Models\User;

it('lists people with pagination', function () {
    Person::factory()->count(3)->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/people')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('rejects listing people without authentication', function () {
    $this->getJson('/api/v1/people')->assertUnauthorized();
});

it('filters people by team_id', function () {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();

    Person::factory()->create(['team_id' => $teamA->id]);
    Person::factory()->create(['team_id' => $teamB->id]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/people?filters[team_id]={$teamA->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.team_id', $teamA->id);
});

it('orders people by multiple columns', function () {
    Person::factory()->create(['name' => 'Beta']);
    Person::factory()->create(['name' => 'Alpha']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/people?order[name]=asc&order[created_at]=desc')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Alpha')
        ->assertJsonPath('data.1.name', 'Beta');
});

it('shows a person', function () {
    $person = Person::factory()->create(['name' => 'Ada Lovelace']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/people/{$person->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $person->id)
        ->assertJsonPath('data.name', 'Ada Lovelace');
});

it('returns 404 for a non-existent person', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/people/999999')
        ->assertNotFound();
});

it('creates a person linked to an existing team', function () {
    $team = Team::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', ['name' => 'Ada Lovelace', 'team_id' => $team->id])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Ada Lovelace')
        ->assertJsonPath('data.team_id', $team->id);
});

it('rejects a person with a non-existent team_id', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', ['name' => 'Ada Lovelace', 'team_id' => 999999])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('team_id');
});

it('updates a person', function () {
    $person = Person::factory()->create(['name' => 'Old Name']);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/people/{$person->id}", ['name' => 'New Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name');

    expect($person->refresh()->name)->toBe('New Name');
});

it('returns 404 when updating a non-existent person', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson('/api/v1/people/999999', ['name' => 'New Name'])
        ->assertNotFound();
});

it('deletes a person', function () {
    $person = Person::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->deleteJson("/api/v1/people/{$person->id}")
        ->assertNoContent();

    expect(Person::query()->find($person->id))->toBeNull();
});

it('returns 404 when deleting a non-existent person', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->deleteJson('/api/v1/people/999999')
        ->assertNotFound();
});

<?php

use App\Models\DailyMeetingEntry;
use App\Models\Person;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;

function validPersonPayload(int $teamId, array $overrides = []): array
{
    return array_merge([
        'name' => 'Ada Lovelace',
        'team_id' => $teamId,
        'birth_date' => '1990-05-10',
        'position' => 'Software Engineer',
        'contract_type' => 'clt',
        'admission_date' => '2020-01-15',
        'seniority' => 'senior',
        'email' => 'ada@example.com',
        'phone' => '+55 11 99999-0000',
    ], $overrides);
}

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

it('shows a person with age computed from birth_date', function () {
    $person = Person::factory()->create(['name' => 'Ada Lovelace', 'birth_date' => '1990-05-10']);
    DailyMeetingEntry::factory()->create([
        'person_id' => $person->id,
        'team_id' => $person->team_id,
        'allotted_seconds' => 100,
        'actual_seconds' => 100,
    ]);
    DailyMeetingEntry::factory()->create([
        'person_id' => $person->id,
        'team_id' => $person->team_id,
        'allotted_seconds' => 100,
        'actual_seconds' => 120,
    ]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/people/{$person->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $person->id)
        ->assertJsonPath('data.name', 'Ada Lovelace')
        ->assertJsonPath('data.age', Carbon::parse('1990-05-10')->age)
        ->assertJsonPath('data.daily_stats_summary.entry_count', 2)
        ->assertJsonPath('data.daily_stats_summary.average_actual_seconds', 110)
        ->assertJsonPath('data.daily_stats_summary.on_time_percentage', 50)
        ->assertJsonPath('data.daily_stats_summary.burned_percentage', 50)
        ->assertJsonPath('data.daily_stats_summary.spoke_too_little_percentage', 0);
});

it('returns 404 for a non-existent person', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/v1/people/999999')
        ->assertNotFound();
});

it('creates a person linked to an existing team', function () {
    $team = Team::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', validPersonPayload($team->id))
        ->assertCreated()
        ->assertJsonPath('data.name', 'Ada Lovelace')
        ->assertJsonPath('data.team_id', $team->id)
        ->assertJsonPath('data.contract_type', 'clt')
        ->assertJsonPath('data.seniority', 'senior');
});

it('creates a person without birth_date or admission_date', function () {
    $team = Team::factory()->create();

    $payload = validPersonPayload($team->id, [
        'birth_date' => null,
        'admission_date' => null,
    ]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', $payload)
        ->assertCreated()
        ->assertJsonPath('data.birth_date', null)
        ->assertJsonPath('data.admission_date', null)
        ->assertJsonPath('data.age', null);
});

it('rejects a person with a non-existent team_id', function () {
    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', validPersonPayload(999999))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('team_id');
});

it('rejects a person with an invalid contract_type', function () {
    $team = Team::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', validPersonPayload($team->id, ['contract_type' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('contract_type');
});

it('rejects a person with an invalid seniority', function () {
    $team = Team::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/people', validPersonPayload($team->id, ['seniority' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('seniority');
});

it('rejects a person with a birth_date in the future', function () {
    $team = Team::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson(
            '/api/v1/people',
            validPersonPayload($team->id, ['birth_date' => now()->addDay()->toDateString()])
        )
        ->assertUnprocessable()
        ->assertJsonValidationErrors('birth_date');
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

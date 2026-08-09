<?php

use App\Enums\DailyAnnotationType;
use App\Models\DailyMeeting;
use App\Models\DailyMeetingAnnotation;
use App\Models\DailyMeetingEntry;
use App\Models\Person;
use App\Models\Team;
use App\Models\User;

function validDailyMeetingPayload(array $personIds, array $overrides = []): array
{
    return array_merge([
        'time_limit_seconds' => 90,
        'started_at' => now()->subMinutes(15)->toIso8601String(),
        'ended_at' => now()->toIso8601String(),
        'entries' => collect($personIds)->map(fn (int $personId) => [
            'person_id' => $personId,
            'actual_seconds' => 60,
        ])->all(),
    ], $overrides);
}

it('creates a daily meeting with its entries in one request', function () {
    $team = Team::factory()->create();
    $people = Person::factory()->count(3)->create(['team_id' => $team->id]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', validDailyMeetingPayload($people->pluck('id')->all()))
        ->assertCreated();

    $response->assertJsonPath('data.team_id', $team->id)
        ->assertJsonPath('data.time_limit_seconds', 90)
        ->assertJsonCount(3, 'data.entries')
        ->assertJsonMissingPath('data.entries.0.note_type')
        ->assertJsonMissingPath('data.entries.0.note');

    $meetingId = $response->json('data.id');
    expect(DailyMeeting::query()->find($meetingId))->not->toBeNull();

    foreach ($people as $index => $person) {
        $this->assertDatabaseHas('daily_meeting_entries', [
            'daily_meeting_id' => $meetingId,
            'team_id' => $team->id,
            'person_id' => $person->id,
            'speaking_order' => $index,
            'allotted_seconds' => 90,
            'actual_seconds' => 60,
        ]);
    }
});

it('saves daily annotations as topics and blockers', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    $payload = validDailyMeetingPayload([$person->id], [
        'annotations' => [
            [
                'type' => 'topico',
                'text' => 'Webhook validado.',
                'person_id' => $person->id,
                'resolved' => true,
            ],
            [
                'type' => 'bloqueio',
                'text' => 'Credencial de staging pendente.',
                'resolved' => true,
            ],
        ],
    ]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', $payload)
        ->assertCreated()
        ->assertJsonCount(2, 'data.annotations');

    $response->assertJsonFragment([
        'type' => 'topico',
        'text' => 'Webhook validado.',
        'person_id' => $person->id,
        'resolved' => false,
    ])->assertJsonFragment([
        'type' => 'bloqueio',
        'text' => 'Credencial de staging pendente.',
        'resolved' => true,
    ]);

    $this->assertDatabaseHas('daily_meeting_annotations', [
        'person_id' => $person->id,
        'type' => 'topico',
        'text' => 'Webhook validado.',
        'resolved' => false,
    ]);
    $this->assertDatabaseHas('daily_meeting_annotations', [
        'person_id' => null,
        'type' => 'bloqueio',
        'text' => 'Credencial de staging pendente.',
        'resolved' => true,
    ]);
});

it('rejects an annotation without a valid type', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    $payload = validDailyMeetingPayload([$person->id], [
        'annotations' => [
            ['text' => 'Alguma anotação.'],
        ],
    ]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('annotations.0.type');
});

it('creates a daily meeting with people from different teams', function () {
    $team = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);
    $outsider = Person::factory()->create(['team_id' => $otherTeam->id]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', validDailyMeetingPayload([$person->id, $outsider->id]))
        ->assertCreated();

    $meetingId = $response->json('data.id');

    $response->assertJsonPath('data.team_id', null)
        ->assertJsonCount(2, 'data.entries');

    $this->assertDatabaseHas('daily_meeting_entries', [
        'daily_meeting_id' => $meetingId,
        'team_id' => $team->id,
        'person_id' => $person->id,
    ]);
    $this->assertDatabaseHas('daily_meeting_entries', [
        'daily_meeting_id' => $meetingId,
        'team_id' => $otherTeam->id,
        'person_id' => $outsider->id,
    ]);
});

it('rejects duplicate person_id entries', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    $payload = validDailyMeetingPayload([$person->id, $person->id]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', $payload)
        ->assertUnprocessable();
});

it('rejects a time_limit_seconds below 60', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    $payload = validDailyMeetingPayload([$person->id], ['time_limit_seconds' => 59]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('time_limit_seconds');
});

it('rejects a time_limit_seconds that is not a multiple of 30', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    $payload = validDailyMeetingPayload([$person->id], ['time_limit_seconds' => 61]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('time_limit_seconds');
});

it('accepts a time_limit_seconds that is a multiple of 30', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    $payload = validDailyMeetingPayload([$person->id], ['time_limit_seconds' => 120]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/daily-meetings', $payload)
        ->assertCreated();
});

it('lists daily meetings filtered by team_id', function () {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();

    DailyMeeting::factory()->create(['team_id' => $teamA->id]);
    DailyMeeting::factory()->create(['team_id' => $teamB->id]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/daily-meetings?filters[team_id]={$teamA->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.team_id', $teamA->id);
});

it('shows a daily meeting with its entries and annotations', function () {
    $meeting = DailyMeeting::factory()->create();
    $entry = DailyMeetingEntry::factory()->forMeeting($meeting)->create();
    $annotation = DailyMeetingAnnotation::factory()->create([
        'daily_meeting_id' => $meeting->id,
        'person_id' => $entry->person_id,
        'type' => DailyAnnotationType::Blocker,
        'text' => 'Aguardando staging.',
    ]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/daily-meetings/{$meeting->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data.entries')
        ->assertJsonCount(1, 'data.annotations')
        ->assertJsonPath('data.entries.0.person.id', $entry->person_id)
        ->assertJsonPath('data.entries.0.person.name', $entry->person->name)
        ->assertJsonMissingPath('data.entries.0.note_type')
        ->assertJsonMissingPath('data.entries.0.note')
        ->assertJsonPath('data.annotations.0.person.id', $entry->person_id)
        ->assertJsonPath('data.annotations.0.person.name', $entry->person->name)
        ->assertJsonPath('data.annotations.0.type', $annotation->type->value)
        ->assertJsonPath('data.annotations.0.text', 'Aguardando staging.');
});

it('does not route update or destroy for daily meetings', function () {
    $meeting = DailyMeeting::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/daily-meetings/{$meeting->id}", ['time_limit_seconds' => 120])
        ->assertMethodNotAllowed();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->deleteJson("/api/v1/daily-meetings/{$meeting->id}")
        ->assertMethodNotAllowed();
});

it('rejects creating a daily meeting without authentication', function () {
    $this->postJson('/api/v1/daily-meetings', [])->assertUnauthorized();
});

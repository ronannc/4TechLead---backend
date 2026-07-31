<?php

use App\Models\DailyMeeting;
use App\Models\DailyMeetingEntry;
use App\Models\Person;
use App\Models\Team;
use App\Models\User;

it('lists entries filtered by team_id', function () {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();

    DailyMeetingEntry::factory()->create(['team_id' => $teamA->id]);
    DailyMeetingEntry::factory()->create(['team_id' => $teamB->id]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/daily-meeting-entries?filters[team_id]={$teamA->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('lists entries filtered by person_id', function () {
    $person = Person::factory()->create();
    DailyMeetingEntry::factory()->create(['person_id' => $person->id]);
    DailyMeetingEntry::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/daily-meeting-entries?filters[person_id]={$person->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('shows a single entry with its computed status', function () {
    $entry = DailyMeetingEntry::factory()->create(['allotted_seconds' => 90, 'actual_seconds' => 100]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/daily-meeting-entries/{$entry->id}")
        ->assertOk()
        ->assertJsonPath('data.status', 'queimado');
});

it('does not route create, update, or destroy for entries', function () {
    $meeting = DailyMeeting::factory()->create();
    $entry = DailyMeetingEntry::factory()->forMeeting($meeting)->create();

    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')->postJson('/api/v1/daily-meeting-entries', [])->assertMethodNotAllowed();
    $this->actingAs($user, 'sanctum')->putJson("/api/v1/daily-meeting-entries/{$entry->id}", [])->assertMethodNotAllowed();
    $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/daily-meeting-entries/{$entry->id}")->assertMethodNotAllowed();
});

it('rejects listing entries without authentication', function () {
    $this->getJson('/api/v1/daily-meeting-entries')->assertUnauthorized();
});

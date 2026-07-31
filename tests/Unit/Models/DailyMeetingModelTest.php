<?php

use App\Models\DailyMeeting;
use App\Models\DailyMeetingEntry;
use App\Models\Team;

it('belongs to a team', function () {
    $team = Team::factory()->create();
    $meeting = DailyMeeting::factory()->create(['team_id' => $team->id]);

    expect($meeting->team->id)->toBe($team->id);
});

it('eager-loads its entries by default', function () {
    $meeting = DailyMeeting::factory()->create();
    DailyMeetingEntry::factory()->forMeeting($meeting)->create();

    $fresh = DailyMeeting::query()->find($meeting->id);

    expect($fresh->relationLoaded('entries'))->toBeTrue()
        ->and($fresh->entries)->toHaveCount(1);
});

it('filters by team_id via the filter scope', function () {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();

    DailyMeeting::factory()->create(['team_id' => $teamA->id]);
    DailyMeeting::factory()->create(['team_id' => $teamB->id]);

    $result = DailyMeeting::query()->filter(['team_id' => $teamA->id])->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->team_id)->toBe($teamA->id);
});

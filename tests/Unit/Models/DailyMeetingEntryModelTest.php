<?php

use App\Enums\DailyEntryStatus;
use App\Models\DailyMeetingEntry;

it('is on_time when actual_seconds is within range', function () {
    $entry = DailyMeetingEntry::factory()->create(['allotted_seconds' => 100, 'actual_seconds' => 100]);

    expect($entry->status)->toBe(DailyEntryStatus::OnTime);
});

it('is on_time exactly at the spoke-too-little boundary', function () {
    // actual == allotted * ratio should NOT be spoke_too_little — the comparator is strict `<`.
    $entry = DailyMeetingEntry::factory()->create(['allotted_seconds' => 100, 'actual_seconds' => 20]);

    expect($entry->status)->toBe(DailyEntryStatus::OnTime);
});

it('is spoke_too_little just below the boundary', function () {
    $entry = DailyMeetingEntry::factory()->create(['allotted_seconds' => 100, 'actual_seconds' => 19]);

    expect($entry->status)->toBe(DailyEntryStatus::SpokeTooLittle);
});

it('is burned when actual_seconds exceeds allotted_seconds', function () {
    $entry = DailyMeetingEntry::factory()->create(['allotted_seconds' => 100, 'actual_seconds' => 101]);

    expect($entry->status)->toBe(DailyEntryStatus::Burned);
});

it('belongs to a meeting, team, and person', function () {
    $entry = DailyMeetingEntry::factory()->create();

    expect($entry->dailyMeeting)->not->toBeNull()
        ->and($entry->team)->not->toBeNull()
        ->and($entry->person)->not->toBeNull();
});

it('filters by person_id via the filter scope', function () {
    $entry = DailyMeetingEntry::factory()->create();
    DailyMeetingEntry::factory()->create();

    $result = DailyMeetingEntry::query()->filter(['person_id' => $entry->person_id])->get();
    expect($result)->toHaveCount(1);
});

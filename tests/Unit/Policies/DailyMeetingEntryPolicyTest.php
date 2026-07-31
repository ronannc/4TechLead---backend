<?php

use App\Models\DailyMeetingEntry;
use App\Models\User;
use App\Policies\DailyMeetingEntryPolicy;

beforeEach(function () {
    $this->policy = new DailyMeetingEntryPolicy;
    $this->user = User::factory()->create();
});

it('allows viewing any daily meeting entries', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows viewing a daily meeting entry', function () {
    $entry = DailyMeetingEntry::factory()->create();

    expect($this->policy->view($this->user, $entry))->toBeTrue();
});

it('denies creating, updating, deleting, restoring, and force-deleting a daily meeting entry', function () {
    $entry = DailyMeetingEntry::factory()->create();

    expect($this->policy->create($this->user))->toBeFalse()
        ->and($this->policy->update($this->user, $entry))->toBeFalse()
        ->and($this->policy->delete($this->user, $entry))->toBeFalse()
        ->and($this->policy->restore($this->user, $entry))->toBeFalse()
        ->and($this->policy->forceDelete($this->user, $entry))->toBeFalse();
});

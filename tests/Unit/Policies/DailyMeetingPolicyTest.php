<?php

use App\Models\DailyMeeting;
use App\Models\User;
use App\Policies\DailyMeetingPolicy;

beforeEach(function () {
    $this->policy = new DailyMeetingPolicy;
    $this->user = User::factory()->create();
});

it('allows viewing any daily meetings', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows viewing a daily meeting', function () {
    $meeting = DailyMeeting::factory()->create();

    expect($this->policy->view($this->user, $meeting))->toBeTrue();
});

it('allows creating a daily meeting', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

it('denies updating, deleting, restoring, and force-deleting a daily meeting', function () {
    $meeting = DailyMeeting::factory()->create();

    expect($this->policy->update($this->user, $meeting))->toBeFalse()
        ->and($this->policy->delete($this->user, $meeting))->toBeFalse()
        ->and($this->policy->restore($this->user, $meeting))->toBeFalse()
        ->and($this->policy->forceDelete($this->user, $meeting))->toBeFalse();
});

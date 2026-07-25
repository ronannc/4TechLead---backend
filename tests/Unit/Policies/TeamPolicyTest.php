<?php

use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;

beforeEach(function () {
    $this->policy = new TeamPolicy;
    $this->user = User::factory()->create();
});

it('allows viewing any teams', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows viewing a team', function () {
    $team = Team::factory()->create();

    expect($this->policy->view($this->user, $team))->toBeTrue();
});

it('allows creating a team', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

it('allows updating a team', function () {
    $team = Team::factory()->create();

    expect($this->policy->update($this->user, $team))->toBeTrue();
});

it('allows deleting a team', function () {
    $team = Team::factory()->create();

    expect($this->policy->delete($this->user, $team))->toBeTrue();
});

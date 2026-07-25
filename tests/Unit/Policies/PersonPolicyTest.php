<?php

use App\Models\Person;
use App\Models\User;
use App\Policies\PersonPolicy;

beforeEach(function () {
    $this->policy = new PersonPolicy;
    $this->user = User::factory()->create();
});

it('allows viewing any people', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows viewing a person', function () {
    $person = Person::factory()->create();

    expect($this->policy->view($this->user, $person))->toBeTrue();
});

it('allows creating a person', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

it('allows updating a person', function () {
    $person = Person::factory()->create();

    expect($this->policy->update($this->user, $person))->toBeTrue();
});

it('allows deleting a person', function () {
    $person = Person::factory()->create();

    expect($this->policy->delete($this->user, $person))->toBeTrue();
});

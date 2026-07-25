<?php

use App\Models\Person;
use App\Models\Team;

it('has many people', function () {
    $team = Team::factory()->create();
    $people = Person::factory()->count(2)->create(['team_id' => $team->id]);

    expect($team->people()->pluck('id')->sort()->values()->all())
        ->toBe($people->pluck('id')->sort()->values()->all());
});

it('filters by name via the filter scope', function () {
    Team::factory()->create(['name' => 'Engineering']);
    Team::factory()->create(['name' => 'Sales']);

    $result = Team::query()->filter(['name' => 'Engineering'])->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->name)->toBe('Engineering');
});

it('ignores filter keys that are not in the allow-list', function () {
    Team::factory()->count(2)->create();

    $result = Team::query()->filter(['id' => 999999])->get();

    expect($result)->toHaveCount(2);
});

it('searches by name via the search scope', function () {
    Team::factory()->create(['name' => 'Engineering']);
    Team::factory()->create(['name' => 'Sales']);

    $result = Team::query()->search('Engin')->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->name)->toBe('Engineering');
});

it('orders by name via the order scope', function () {
    Team::factory()->create(['name' => 'Beta']);
    Team::factory()->create(['name' => 'Alpha']);

    $result = Team::query()->order(['name' => 'asc'])->get();

    expect($result->pluck('name')->all())->toBe(['Alpha', 'Beta']);
});

it('defaults to latest first when no order is given', function () {
    $first = Team::factory()->create(['created_at' => now()->subMinute()]);
    $second = Team::factory()->create(['created_at' => now()]);

    $result = Team::query()->order()->get();

    expect($result->first()->id)->toBe($second->id);
});

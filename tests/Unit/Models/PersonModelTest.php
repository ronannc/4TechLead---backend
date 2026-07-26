<?php

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use App\Models\Person;
use App\Models\Team;

it('computes age from birth_date', function () {
    $person = Person::factory()->create(['birth_date' => now()->subYears(30)->toDateString()]);

    expect($person->age)->toBe(30);
});

it('returns null age when birth_date is null', function () {
    $person = Person::factory()->create(['birth_date' => null]);

    expect($person->age)->toBeNull();
});

it('casts contract_type and seniority to their enums', function () {
    $person = Person::factory()->create([
        'contract_type' => ContractType::Pj,
        'seniority' => SeniorityLevel::Senior,
    ]);

    expect($person->contract_type)->toBe(ContractType::Pj)
        ->and($person->seniority)->toBe(SeniorityLevel::Senior);
});

it('belongs to a team', function () {
    $team = Team::factory()->create();
    $person = Person::factory()->create(['team_id' => $team->id]);

    expect($person->team->id)->toBe($team->id);
});

it('filters by team_id via the filter scope', function () {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();

    Person::factory()->create(['team_id' => $teamA->id]);
    Person::factory()->create(['team_id' => $teamB->id]);

    $result = Person::query()->filter(['team_id' => $teamA->id])->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->team_id)->toBe($teamA->id);
});

it('ignores filter keys that are not in the allow-list', function () {
    Person::factory()->count(2)->create();

    $result = Person::query()->filter(['name' => 'anything'])->get();

    expect($result)->toHaveCount(2);
});

it('searches by name via the search scope', function () {
    Person::factory()->create(['name' => 'Ada Lovelace']);
    Person::factory()->create(['name' => 'Grace Hopper']);

    $result = Person::query()->search('Lovelace')->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->name)->toBe('Ada Lovelace');
});

it('orders by name via the order scope', function () {
    Person::factory()->create(['name' => 'Beta']);
    Person::factory()->create(['name' => 'Alpha']);

    $result = Person::query()->order(['name' => 'asc'])->get();

    expect($result->pluck('name')->all())->toBe(['Alpha', 'Beta']);
});

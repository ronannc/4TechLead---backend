<?php

arch('controllers do not access the database or Eloquent builder directly')
    ->expect('App\Http\Controllers')
    ->not->toUse(['Illuminate\Support\Facades\DB', 'Illuminate\Database\Eloquent\Builder']);

arch('CRUD resource controllers use the CrudControllerTrait trait')
    ->expect([
        'App\Http\Controllers\Api\V1\TeamController',
        'App\Http\Controllers\Api\V1\PersonController',
        'App\Http\Controllers\Api\V1\DailyMeetingController',
        'App\Http\Controllers\Api\V1\DailyMeetingEntryController',
    ])
    ->toUseTrait('App\Http\Controllers\Concerns\CrudControllerTrait');

arch('generic services implement their matching contract')
    ->expect('App\Services\GenericStoreService')->toImplement('App\Contracts\Services\StoreServiceContract')
    ->expect('App\Services\GenericUpdateService')->toImplement('App\Contracts\Services\UpdateServiceContract')
    ->expect('App\Services\GenericDeleteService')->toImplement('App\Contracts\Services\DeleteServiceContract')
    ->expect('App\Services\GenericIndexService')->toImplement('App\Contracts\Services\IndexServiceContract');

arch('the custom DailyMeetingStoreService implements StoreServiceContract')
    ->expect('App\Services\DailyMeetingStoreService')
    ->toImplement('App\Contracts\Services\StoreServiceContract');

arch('Team, Person, and Daily models use the Filterable trait')
    ->expect(['App\Models\Team', 'App\Models\Person', 'App\Models\DailyMeeting', 'App\Models\DailyMeetingEntry'])
    ->toUseTrait('App\Models\Concerns\Filterable');

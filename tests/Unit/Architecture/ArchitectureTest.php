<?php

arch('controllers do not access the database or Eloquent builder directly')
    ->expect('App\Http\Controllers')
    ->not->toUse(['Illuminate\Support\Facades\DB', 'Illuminate\Database\Eloquent\Builder']);

arch('controllers use the CrudControllerTrait trait')
    ->expect('App\Http\Controllers\Api\V1')
    ->toUseTrait('App\Http\Controllers\Concerns\CrudControllerTrait');

arch('generic services implement their matching contract')
    ->expect('App\Services\GenericStoreService')->toImplement('App\Contracts\Services\StoreServiceContract')
    ->expect('App\Services\GenericUpdateService')->toImplement('App\Contracts\Services\UpdateServiceContract')
    ->expect('App\Services\GenericDeleteService')->toImplement('App\Contracts\Services\DeleteServiceContract')
    ->expect('App\Services\GenericIndexService')->toImplement('App\Contracts\Services\IndexServiceContract');

arch('Team and Person models use the Filterable trait')
    ->expect(['App\Models\Team', 'App\Models\Person'])
    ->toUseTrait('App\Models\Concerns\Filterable');

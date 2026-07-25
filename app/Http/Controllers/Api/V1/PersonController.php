<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Person\IndexPersonRequest;
use App\Http\Requests\Person\StorePersonRequest;
use App\Http\Requests\Person\UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;

final class PersonController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = Person::class;
        $this->resource = PersonResource::class;
        $this->storeRequest = StorePersonRequest::class;
        $this->updateRequest = UpdatePersonRequest::class;
        $this->indexRequest = IndexPersonRequest::class;
    }
}

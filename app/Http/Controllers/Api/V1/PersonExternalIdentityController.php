<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonExternalIdentity\IndexPersonExternalIdentityRequest;
use App\Http\Requests\PersonExternalIdentity\StorePersonExternalIdentityRequest;
use App\Http\Requests\PersonExternalIdentity\UpdatePersonExternalIdentityRequest;
use App\Http\Resources\PersonExternalIdentityResource;
use App\Models\PersonExternalIdentity;

final class PersonExternalIdentityController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = PersonExternalIdentity::class;
        $this->resource = PersonExternalIdentityResource::class;
        $this->storeRequest = StorePersonExternalIdentityRequest::class;
        $this->updateRequest = UpdatePersonExternalIdentityRequest::class;
        $this->indexRequest = IndexPersonExternalIdentityRequest::class;
    }
}

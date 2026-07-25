<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\IndexTeamRequest;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;

final class TeamController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = Team::class;
        $this->resource = TeamResource::class;
        $this->storeRequest = StoreTeamRequest::class;
        $this->updateRequest = UpdateTeamRequest::class;
        $this->indexRequest = IndexTeamRequest::class;
    }
}

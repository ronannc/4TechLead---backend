<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ListParams;
use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\IndexTeamRequest;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\PersonResource;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function show(int|string $id, Request $request): JsonResponse
    {
        $team = Team::query()->findOrFail($id);

        $this->authorize('view', $team);

        $params = new ListParams(
            page: max(1, $request->integer('people_page', 1)),
            perPage: min(100, max(1, $request->integer('people_per_page', 15))),
            search: $request->string('people_search')->trim()->value() ?: null,
        );

        $people = $team->people()
            ->search($params->search)
            ->order(['name' => 'asc'])
            ->paginate(perPage: $params->perPage, page: $params->page);

        $team->setAttribute('people', PersonResource::collection($people->items())->resolve());
        $team->setAttribute('people_meta', [
            'current_page' => $people->currentPage(),
            'last_page' => $people->lastPage(),
            'per_page' => $people->perPage(),
            'total' => $people->total(),
        ]);

        return (new TeamResource($team))->response();
    }
}

<?php

namespace App\Http\Controllers\Concerns;

use App\Contracts\Services\DeleteServiceContract;
use App\Contracts\Services\IndexServiceContract;
use App\Contracts\Services\StoreServiceContract;
use App\Contracts\Services\UpdateServiceContract;
use App\DTOs\ListParams;
use App\Services\GenericDeleteService;
use App\Services\GenericIndexService;
use App\Services\GenericStoreService;
use App\Services\GenericUpdateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Implements the full apiResource CRUD (index/store/show/update/destroy) for a model.
 *
 * The consuming controller only needs to set $this->model (and optionally
 * $this->resource / $this->{store,update,index}Request) in its constructor.
 * Everything else falls back to the Generic*Service classes, parameterized by
 * $this->model. A module that needs custom logic for a single operation sets
 * the matching $this->{store,update,delete,index}Service property to its own
 * implementation of the corresponding contract.
 */
trait CrudControllerTrait
{
    /**
     * @var class-string<Model>
     */
    protected string $model;

    protected ?string $resource = null;

    protected ?string $storeRequest = null;

    protected ?string $updateRequest = null;

    protected ?string $indexRequest = null;

    protected ?StoreServiceContract $storeService = null;

    protected ?UpdateServiceContract $updateService = null;

    protected ?DeleteServiceContract $deleteService = null;

    protected ?IndexServiceContract $indexService = null;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', $this->model);
        $this->validateWith($this->indexRequest);

        $paginator = $this->resolveIndexService()->index(ListParams::fromRequest(request()));

        return $this->resourceClass()::collection($paginator)->response();
    }

    public function store(): JsonResponse
    {
        $this->authorize('create', $this->model);

        $model = $this->resolveStoreService()->store($this->validateWith($this->storeRequest));

        return new ($this->resourceClass())($model)->response()->setStatusCode(201);
    }

    public function show(int|string $id): JsonResponse
    {
        $model = $this->findModel($id);

        $this->authorize('view', $model);

        return new ($this->resourceClass())($model)->response();
    }

    public function update(int|string $id): JsonResponse
    {
        $model = $this->findModel($id);

        $this->authorize('update', $model);

        $model = $this->resolveUpdateService()->update($model, $this->validateWith($this->updateRequest));

        return new ($this->resourceClass())($model)->response();
    }

    public function destroy(int|string $id): JsonResponse
    {
        $model = $this->findModel($id);

        $this->authorize('delete', $model);

        $this->resolveDeleteService()->delete($model);

        return response()->json(status: 204);
    }

    protected function findModel(int|string $id): Model
    {
        return $this->model::query()->findOrFail($id);
    }

    protected function resourceClass(): string
    {
        return $this->resource ?? JsonResource::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateWith(?string $requestClass): array
    {
        if ($requestClass === null) {
            return request()->all();
        }

        /** @var FormRequest $request */
        $request = app($requestClass);
        $request->validateResolved();

        return $request->validated();
    }

    protected function resolveIndexService(): IndexServiceContract
    {
        return $this->indexService ??= app(GenericIndexService::class, ['model' => $this->model]);
    }

    protected function resolveStoreService(): StoreServiceContract
    {
        return $this->storeService ??= app(GenericStoreService::class, ['model' => $this->model]);
    }

    protected function resolveUpdateService(): UpdateServiceContract
    {
        return $this->updateService ??= app(GenericUpdateService::class);
    }

    protected function resolveDeleteService(): DeleteServiceContract
    {
        return $this->deleteService ??= app(GenericDeleteService::class);
    }
}

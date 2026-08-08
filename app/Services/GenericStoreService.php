<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

final class GenericStoreService implements StoreServiceContract
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model) {}

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(fn (): Model => $this->model::query()->create($attributes));
    }
}

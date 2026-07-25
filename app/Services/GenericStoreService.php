<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class GenericStoreService implements StoreServiceContract
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(fn (): Model => $this->model::query()->create($attributes));
    }
}

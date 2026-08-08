<?php

namespace App\Services;

use App\Contracts\Services\UpdateServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

final class GenericUpdateService implements UpdateServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(Model $model, array $attributes): Model
    {
        return DB::transaction(function () use ($model, $attributes): Model {
            $model->fill($attributes)->save();

            return $model->refresh();
        });
    }
}

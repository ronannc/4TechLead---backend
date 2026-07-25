<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Model;

interface UpdateServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Model $model, array $attributes): Model;
}

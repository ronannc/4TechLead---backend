<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Model;

interface DeleteServiceContract
{
    public function delete(Model $model): bool;
}

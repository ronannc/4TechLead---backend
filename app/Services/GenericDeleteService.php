<?php

namespace App\Services;

use App\Contracts\Services\DeleteServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class GenericDeleteService implements DeleteServiceContract
{
    public function delete(Model $model): bool
    {
        return DB::transaction(fn (): bool => (bool) $model->delete());
    }
}

<?php

namespace App\Contracts\Services;

use App\DTOs\ListParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IndexServiceContract
{
    public function index(ListParams $params): LengthAwarePaginator;
}

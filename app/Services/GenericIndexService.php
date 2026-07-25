<?php

namespace App\Services;

use App\Contracts\Services\IndexServiceContract;
use App\DTOs\ListParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

final class GenericIndexService implements IndexServiceContract
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model) {}

    public function index(ListParams $params): LengthAwarePaginator
    {
        return $this->model::query()
            ->filter($params->filters)
            ->search($params->search)
            ->order($params->order)
            ->paginate(perPage: $params->perPage, page: $params->page);
    }
}

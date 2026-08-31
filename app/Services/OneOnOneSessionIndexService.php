<?php

namespace App\Services;

use App\Contracts\Services\IndexServiceContract;
use App\DTOs\ListParams;
use App\Models\OneOnOneSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class OneOnOneSessionIndexService implements IndexServiceContract
{
    public function index(ListParams $params): LengthAwarePaginator
    {
        $user = auth()->user();

        $query = OneOnOneSession::query()
            ->filter($params->filters)
            ->search($params->search)
            ->order($params->order);

        if ($user?->isMember()) {
            $query->where('person_id', $user->person_id);
        }

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }
}

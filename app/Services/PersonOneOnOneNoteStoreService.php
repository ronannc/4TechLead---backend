<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceContract;
use App\Models\PersonOneOnOneNote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class PersonOneOnOneNoteStoreService implements StoreServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes): PersonOneOnOneNote {
            $attributes['created_by'] ??= auth()->id();
            $attributes['status'] ??= 'open';

            return PersonOneOnOneNote::query()->create($attributes);
        });
    }
}

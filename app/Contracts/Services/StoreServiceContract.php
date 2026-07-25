<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Model;

interface StoreServiceContract
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(array $attributes): Model;
}

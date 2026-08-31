<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonOneOnOneNote\IndexPersonOneOnOneNoteRequest;
use App\Http\Requests\PersonOneOnOneNote\StorePersonOneOnOneNoteRequest;
use App\Http\Requests\PersonOneOnOneNote\UpdatePersonOneOnOneNoteRequest;
use App\Http\Resources\PersonOneOnOneNoteResource;
use App\Models\PersonOneOnOneNote;
use App\Services\PersonOneOnOneNoteStoreService;

class PersonOneOnOneNoteController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = PersonOneOnOneNote::class;
        $this->resource = PersonOneOnOneNoteResource::class;
        $this->storeRequest = StorePersonOneOnOneNoteRequest::class;
        $this->updateRequest = UpdatePersonOneOnOneNoteRequest::class;
        $this->indexRequest = IndexPersonOneOnOneNoteRequest::class;
        $this->storeService = app(PersonOneOnOneNoteStoreService::class);
    }
}

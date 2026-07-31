<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\DailyMeetingEntry\IndexDailyMeetingEntryRequest;
use App\Http\Resources\DailyMeetingEntryResource;
use App\Models\DailyMeetingEntry;

final class DailyMeetingEntryController extends Controller
{
    use CrudControllerTrait;

    public function __construct()
    {
        $this->model = DailyMeetingEntry::class;
        $this->resource = DailyMeetingEntryResource::class;
        $this->indexRequest = IndexDailyMeetingEntryRequest::class;
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\CrudControllerTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\DailyMeeting\IndexDailyMeetingRequest;
use App\Http\Requests\DailyMeeting\StoreDailyMeetingRequest;
use App\Http\Resources\DailyMeetingResource;
use App\Models\DailyMeeting;
use App\Services\DailyMeetingStoreService;

final class DailyMeetingController extends Controller
{
    use CrudControllerTrait;

    public function __construct(DailyMeetingStoreService $storeService)
    {
        $this->model = DailyMeeting::class;
        $this->resource = DailyMeetingResource::class;
        $this->storeRequest = StoreDailyMeetingRequest::class;
        $this->indexRequest = IndexDailyMeetingRequest::class;
        $this->storeService = $storeService;
    }
}

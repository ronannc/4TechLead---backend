<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyMeetingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team_id' => $this->team_id,
            'team' => TeamResource::make($this->whenLoaded('team')),
            'time_limit_seconds' => $this->time_limit_seconds,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'entries' => DailyMeetingEntryResource::collection($this->whenLoaded('entries')),
            'annotations' => DailyMeetingAnnotationResource::collection($this->whenLoaded('annotations')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyMeetingEntryResource extends JsonResource
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
            'daily_meeting_id' => $this->daily_meeting_id,
            'team_id' => $this->team_id,
            'person_id' => $this->person_id,
            'person' => PersonResource::make($this->whenLoaded('person')),
            'speaking_order' => $this->speaking_order,
            'allotted_seconds' => $this->allotted_seconds,
            'actual_seconds' => $this->actual_seconds,
            'status' => $this->status,
            'note_type' => $this->note_type,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

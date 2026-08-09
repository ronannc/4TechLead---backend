<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyMeetingAnnotationResource extends JsonResource
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
            'person_id' => $this->person_id,
            'person' => PersonResource::make($this->whenLoaded('person')),
            'type' => $this->type->value,
            'text' => $this->text,
            'resolved' => $this->resolved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

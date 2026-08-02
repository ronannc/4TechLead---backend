<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OkrResource extends JsonResource
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
            'person_id' => $this->person_id,
            'development_plan_id' => $this->development_plan_id,
            'objective' => $this->objective,
            'cycle' => $this->cycle,
            'status' => $this->status,
            'focus_area' => $this->focus_area,
            'diagnosis' => $this->diagnosis,
            'evidence_source' => $this->evidence_source,
            'baseline' => $this->baseline,
            'target' => $this->target,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'confidence' => $this->confidence,
            'progress' => $this->progress,
            'key_results' => OkrKeyResultResource::collection($this->whenLoaded('keyResults')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OkrKeyResultResource extends JsonResource
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
            'okr_id' => $this->okr_id,
            'title' => $this->title,
            'metric_name' => $this->metric_name,
            'initial_value' => $this->initial_value,
            'current_value' => $this->current_value,
            'target_value' => $this->target_value,
            'unit' => $this->unit,
            'confidence' => $this->confidence,
            'status' => $this->status,
            'due_date' => $this->due_date?->toDateString(),
            'evidence' => $this->evidence,
            'progress' => $this->progress,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

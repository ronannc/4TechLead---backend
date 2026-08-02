<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OneOnOneSessionResource extends JsonResource
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
            'one_on_one_template_id' => $this->one_on_one_template_id,
            'scheduled_for' => $this->scheduled_for?->toDateString(),
            'held_at' => $this->held_at?->toDateString(),
            'title' => $this->title,
            'status' => $this->status,
            'sentiment' => $this->sentiment,
            'questions' => $this->questions,
            'answers' => $this->answers,
            'notes' => $this->notes,
            'action_items' => $this->action_items,
            'template' => OneOnOneTemplateResource::make($this->whenLoaded('template')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

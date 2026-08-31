<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonOneOnOneNoteResource extends JsonResource
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
            'created_by' => $this->created_by,
            'one_on_one_session_id' => $this->one_on_one_session_id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'occurred_at' => $this->occurred_at?->toDateString(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

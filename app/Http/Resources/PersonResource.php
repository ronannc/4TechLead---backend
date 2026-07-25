<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'name' => $this->name,
            'team_id' => $this->team_id,
            'team' => TeamResource::make($this->whenLoaded('team')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

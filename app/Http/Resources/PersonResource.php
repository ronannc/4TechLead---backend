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
            'birth_date' => $this->birth_date?->toDateString(),
            'age' => $this->age,
            'position' => $this->position,
            'contract_type' => $this->contract_type,
            'email' => $this->email,
            'phone' => $this->phone,
            'admission_date' => $this->admission_date?->toDateString(),
            'seniority' => $this->seniority,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'people' => $this->when(
                $this->resource->getAttribute('people') !== null,
                fn (): mixed => $this->resource->getAttribute('people'),
            ),
            'people_meta' => $this->when(
                $this->resource->getAttribute('people_meta') !== null,
                fn (): mixed => $this->resource->getAttribute('people_meta'),
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalNotificationResource extends JsonResource
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
            'integration_system_id' => $this->integration_system_id,
            'integration_system' => $this->whenLoaded('integrationSystem', fn () => [
                'id' => $this->integrationSystem->id,
                'name' => $this->integrationSystem->name,
                'provider' => $this->integrationSystem->provider,
            ]),
            'event_id' => $this->event_id,
            'title' => $this->title,
            'message' => $this->message,
            'content' => $this->message,
            'type' => $this->type,
            'severity' => $this->severity,
            'source_ref' => $this->source_ref,
            'payload' => $this->payload,
            'metadata' => $this->metadata,
            'received_at' => $this->received_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

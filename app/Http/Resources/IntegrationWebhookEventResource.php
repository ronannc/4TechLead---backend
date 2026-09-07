<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationWebhookEventResource extends JsonResource
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
            'person_id' => $this->person_id,
            'event_id' => $this->event_id,
            'event_type' => $this->event_type,
            'external_actor_code' => $this->external_actor_code,
            'status' => $this->status,
            'failure_reason' => $this->failure_reason,
            'payload' => $this->payload,
            'payload_hash' => $this->payload_hash,
            'payload_size_bytes' => $this->payload_size_bytes,
            'normalized_payload' => $this->normalized_payload,
            'received_at' => $this->received_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

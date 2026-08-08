<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonDeliveryMetricResource extends JsonResource
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
            'integration_system_id' => $this->integration_system_id,
            'integration_webhook_event_id' => $this->integration_webhook_event_id,
            'metric_type' => $this->metric_type,
            'metric_value' => $this->metric_value,
            'unit' => $this->unit,
            'source_ref' => $this->source_ref,
            'period_start' => $this->period_start?->toDateString(),
            'period_end' => $this->period_end?->toDateString(),
            'occurred_at' => $this->occurred_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

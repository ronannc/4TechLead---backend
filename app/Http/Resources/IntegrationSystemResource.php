<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class IntegrationSystemResource extends JsonResource
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
            'provider' => $this->provider,
            'description' => $this->description,
            'token_prefix' => $this->token_prefix,
            'webhook_token' => $this->when($this->webhook_token !== null, $this->webhook_token),
            'webhook_url' => $this->when(
                in_array($this->provider, ['github', 'github-actions'], true),
                fn (): string => URL::to('/api/v1/github-webhooks'),
            ),
            'active' => $this->active,
            'last_received_at' => $this->last_received_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

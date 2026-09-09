<?php

namespace App\Http\Requests\IntegrationWebhook;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexIntegrationWebhookEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'filters' => ['sometimes', 'array'],
            'filters.integration_system_id' => ['sometimes', 'integer', TenantRule::exists('integration_systems')],
            'filters.person_id' => ['sometimes', 'integer', TenantRule::exists('people')],
            'filters.event_type' => ['sometimes', 'string', 'max:100'],
            'filters.status' => ['sometimes', 'string', 'max:100'],
            'filters.external_actor_code' => ['sometimes', 'string', 'max:255'],
            'filters.unmapped' => ['sometimes', 'boolean'],
            'filters.with_failure' => ['sometimes', 'boolean'],
            'filters.received_from' => ['sometimes', 'date'],
            'filters.received_to' => ['sometimes', 'date'],
            'order' => ['sometimes', 'array'],
            'order.received_at' => ['sometimes', 'in:asc,desc'],
            'order.created_at' => ['sometimes', 'in:asc,desc'],
            'order.updated_at' => ['sometimes', 'in:asc,desc'],
        ];
    }
}

<?php

namespace App\Http\Requests\PersonDeliveryMetric;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexPersonDeliveryMetricRequest extends FormRequest
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
            'filters.person_id' => ['sometimes', 'integer', TenantRule::exists('people')],
            'filters.integration_system_id' => ['sometimes', 'integer', TenantRule::exists('integration_systems')],
            'filters.integration_webhook_event_id' => ['sometimes', 'integer', TenantRule::exists('integration_webhook_events')],
            'filters.metric_type' => ['sometimes', 'string', 'max:100'],
            'order' => ['sometimes', 'array'],
        ];
    }
}

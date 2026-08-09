<?php

namespace App\Http\Requests\ExternalNotification;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExternalNotificationWebhookRequest extends FormRequest
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
            'event_id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'severity' => ['sometimes', 'string', Rule::in(['info', 'success', 'warning', 'error', 'critical'])],
            'source_ref' => ['sometimes', 'nullable', 'string', 'max:255'],
            'payload' => ['sometimes', 'nullable', 'array'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'occurred_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}

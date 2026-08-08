<?php

namespace App\Http\Requests\PersonExternalIdentity;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonExternalIdentityRequest extends FormRequest
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
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'integration_system_id' => ['required', 'integer', 'exists:integration_systems,id'],
            'external_code' => ['required', 'string', 'max:255'],
            'external_username' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

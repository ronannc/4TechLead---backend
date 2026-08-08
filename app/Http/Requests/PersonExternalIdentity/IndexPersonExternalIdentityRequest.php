<?php

namespace App\Http\Requests\PersonExternalIdentity;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexPersonExternalIdentityRequest extends FormRequest
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
            'filters.person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'filters.integration_system_id' => ['sometimes', 'integer', 'exists:integration_systems,id'],
            'filters.external_code' => ['sometimes', 'string', 'max:255'],
            'filters.active' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'array'],
        ];
    }
}

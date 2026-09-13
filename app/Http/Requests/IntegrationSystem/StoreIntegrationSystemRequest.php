<?php

namespace App\Http\Requests\IntegrationSystem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIntegrationSystemRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'in:github,clickup,custom'],
            'description' => ['nullable', 'string'],
            'provider_api_token' => [
                'nullable',
                'string',
                'max:2048',
                Rule::prohibitedIf(fn (): bool => $this->input('provider') !== 'clickup'),
            ],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

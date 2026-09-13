<?php

namespace App\Http\Requests\IntegrationSystem;

use App\Models\IntegrationSystem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIntegrationSystemRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'provider' => ['sometimes', 'string', 'in:github,clickup,custom'],
            'description' => ['nullable', 'string'],
            'provider_api_token' => [
                'nullable',
                'string',
                'max:2048',
                Rule::prohibitedIf(function (): bool {
                    $integrationSystem = $this->route('integrationSystem');
                    $provider = $this->input('provider')
                        ?? ($integrationSystem instanceof IntegrationSystem
                            ? $integrationSystem->provider
                            : null);

                    return $provider !== 'clickup';
                }),
            ],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

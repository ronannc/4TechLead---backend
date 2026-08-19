<?php

namespace App\Http\Requests\PersonExternalIdentity;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'person_id' => [
                'required',
                'integer',
                TenantRule::exists('people'),
                Rule::unique('person_external_identities', 'person_id')
                    ->where('integration_system_id', $this->integer('integration_system_id')),
            ],
            'integration_system_id' => ['required', 'integer', TenantRule::exists('integration_systems')],
            'metadata' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

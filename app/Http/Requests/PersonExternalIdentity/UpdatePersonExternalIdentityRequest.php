<?php

namespace App\Http\Requests\PersonExternalIdentity;

use App\Models\PersonExternalIdentity;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonExternalIdentityRequest extends FormRequest
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
        $identityId = $this->route('person_external_identity');
        $integrationSystemId = $this->integer('integration_system_id')
            ?: PersonExternalIdentity::query()
                ->whereKey($identityId)
                ->value('integration_system_id');

        return [
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'integration_system_id' => ['sometimes', 'integer', 'exists:integration_systems,id'],
            'external_code' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('person_external_identities', 'external_code')
                    ->where('integration_system_id', $integrationSystemId)
                    ->ignore($identityId),
            ],
            'external_username' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

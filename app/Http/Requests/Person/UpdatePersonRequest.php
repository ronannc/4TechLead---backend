<?php

namespace App\Http\Requests\Person;

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonRequest extends FormRequest
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
            'team_id' => ['sometimes', 'integer', TenantRule::exists('teams')],
            'birth_date' => ['sometimes', 'nullable', 'date', 'before:today'],
            'position' => ['sometimes', 'string', 'max:255'],
            'contract_type' => ['sometimes', Rule::enum(ContractType::class)],
            'admission_date' => [
                'sometimes',
                'nullable',
                'date',
                'before_or_equal:today',
                Rule::when($this->filled('birth_date'), ['after:birth_date']),
            ],
            'seniority' => ['sometimes', Rule::enum(SeniorityLevel::class)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}

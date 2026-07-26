<?php

namespace App\Http\Requests\Person;

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
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
            'team_id' => ['sometimes', 'integer', 'exists:teams,id'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'position' => ['sometimes', 'string', 'max:255'],
            'contract_type' => ['sometimes', Rule::enum(ContractType::class)],
            // No `after:birth_date` here (unlike Store) — a partial update may send
            // `admission_date` without `birth_date` in the same payload, and the cross-field
            // rule would then compare against a missing value.
            'admission_date' => ['sometimes', 'date', 'before_or_equal:today'],
            'seniority' => ['sometimes', Rule::enum(SeniorityLevel::class)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}

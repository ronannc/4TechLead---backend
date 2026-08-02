<?php

namespace App\Http\Requests\Person;

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
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
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'position' => ['required', 'string', 'max:255'],
            'contract_type' => ['required', Rule::enum(ContractType::class)],
            'admission_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
                Rule::when($this->filled('birth_date'), ['after:birth_date']),
            ],
            'seniority' => ['required', Rule::enum(SeniorityLevel::class)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}

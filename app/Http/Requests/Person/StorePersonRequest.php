<?php

namespace App\Http\Requests\Person;

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('github_username')) {
            $this->merge([
                'github_username' => $this->normalizedGithubUsername($this->input('github_username')),
            ]);
        }
    }

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
            'team_id' => ['required', 'integer', TenantRule::exists('teams')],
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
            'github_username' => [
                'nullable',
                'string',
                'max:39',
                'regex:/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,37}[A-Za-z0-9])?$/',
                Rule::unique('people', 'github_username')
                    ->where('tenant_id', auth()->user()?->tenant_id),
            ],
            'clickup_user_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('people', 'clickup_user_id')
                    ->where('tenant_id', auth()->user()?->tenant_id),
            ],
        ];
    }

    protected function normalizedGithubUsername(mixed $value): mixed
    {
        return is_string($value) ? ltrim(trim($value), '@') : $value;
    }
}

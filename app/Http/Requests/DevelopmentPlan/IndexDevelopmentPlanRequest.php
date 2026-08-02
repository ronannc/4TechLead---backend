<?php

namespace App\Http\Requests\DevelopmentPlan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexDevelopmentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'order' => ['sometimes', 'array'],
            'order.*' => ['sometimes', 'string', 'in:asc,desc'],
            'filters.person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'filters.status' => ['sometimes', 'string', 'max:50'],
            'filters.target_seniority' => ['sometimes', 'string', 'max:50'],
        ];
    }
}

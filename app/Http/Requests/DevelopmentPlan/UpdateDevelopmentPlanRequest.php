<?php

namespace App\Http\Requests\DevelopmentPlan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDevelopmentPlanRequest extends FormRequest
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
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:50'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'target_role' => ['sometimes', 'nullable', 'string', 'max:255'],
            'target_seniority' => ['sometimes', 'nullable', 'string', 'max:50'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

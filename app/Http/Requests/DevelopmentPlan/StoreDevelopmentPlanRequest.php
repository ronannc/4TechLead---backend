<?php

namespace App\Http\Requests\DevelopmentPlan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDevelopmentPlanRequest extends FormRequest
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
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'target_role' => ['nullable', 'string', 'max:255'],
            'target_seniority' => ['nullable', 'string', 'max:50'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

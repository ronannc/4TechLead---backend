<?php

namespace App\Http\Requests\Okr;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOkrRequest extends FormRequest
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
            'development_plan_id' => ['sometimes', 'nullable', 'integer', 'exists:development_plans,id'],
            'objective' => ['sometimes', 'string', 'max:255'],
            'cycle' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:50'],
            'focus_area' => ['sometimes', 'nullable', 'string', 'max:255'],
            'diagnosis' => ['sometimes', 'nullable', 'string'],
            'evidence_source' => ['sometimes', 'nullable', 'string'],
            'baseline' => ['sometimes', 'nullable', 'string'],
            'target' => ['sometimes', 'nullable', 'string'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'confidence' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

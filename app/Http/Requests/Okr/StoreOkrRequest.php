<?php

namespace App\Http\Requests\Okr;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOkrRequest extends FormRequest
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
            'development_plan_id' => ['nullable', 'integer', 'exists:development_plans,id'],
            'objective' => ['required', 'string', 'max:255'],
            'cycle' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:50'],
            'focus_area' => ['nullable', 'string', 'max:255'],
            'diagnosis' => ['nullable', 'string'],
            'evidence_source' => ['nullable', 'string'],
            'baseline' => ['nullable', 'string'],
            'target' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'confidence' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

<?php

namespace App\Http\Requests\OkrKeyResult;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOkrKeyResultRequest extends FormRequest
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
            'okr_id' => ['sometimes', 'integer', 'exists:okrs,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'metric_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'initial_value' => ['sometimes', 'nullable', 'numeric'],
            'current_value' => ['sometimes', 'nullable', 'numeric'],
            'target_value' => ['sometimes', 'nullable', 'numeric'],
            'unit' => ['sometimes', 'nullable', 'string', 'max:50'],
            'confidence' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'status' => ['sometimes', 'string', 'max:50'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'evidence' => ['sometimes', 'nullable', 'string'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

<?php

namespace App\Http\Requests\OkrKeyResult;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOkrKeyResultRequest extends FormRequest
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
            'okr_id' => ['required', 'integer', 'exists:okrs,id'],
            'title' => ['required', 'string', 'max:255'],
            'metric_name' => ['nullable', 'string', 'max:255'],
            'initial_value' => ['nullable', 'numeric'],
            'current_value' => ['nullable', 'numeric'],
            'target_value' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
            'confidence' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'status' => ['sometimes', 'string', 'max:50'],
            'due_date' => ['nullable', 'date'],
            'evidence' => ['nullable', 'string'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

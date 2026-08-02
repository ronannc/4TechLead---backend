<?php

namespace App\Http\Requests\OkrKeyResult;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexOkrKeyResultRequest extends FormRequest
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
            'filters.okr_id' => ['sometimes', 'integer', 'exists:okrs,id'],
            'filters.status' => ['sometimes', 'string', 'max:50'],
            'filters.metric_name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}

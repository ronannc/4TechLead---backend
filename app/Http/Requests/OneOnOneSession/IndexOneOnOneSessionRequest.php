<?php

namespace App\Http\Requests\OneOnOneSession;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexOneOnOneSessionRequest extends FormRequest
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
            'filters.one_on_one_template_id' => ['sometimes', 'integer', 'exists:one_on_one_templates,id'],
            'filters.status' => ['sometimes', 'string', 'max:50'],
            'filters.sentiment' => ['sometimes', 'string', 'max:50'],
        ];
    }
}

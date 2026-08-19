<?php

namespace App\Http\Requests\OneOnOneSession;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOneOnOneSessionRequest extends FormRequest
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
            'person_id' => ['sometimes', 'integer', TenantRule::exists('people')],
            'one_on_one_template_id' => ['sometimes', 'nullable', 'integer', TenantRule::exists('one_on_one_templates')],
            'scheduled_for' => ['sometimes', 'nullable', 'date'],
            'held_at' => ['sometimes', 'nullable', 'date'],
            'title' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:50'],
            'sentiment' => ['sometimes', 'nullable', 'string', 'max:50'],
            'questions' => ['sometimes', 'nullable', 'array'],
            'questions.*' => ['required', 'string', 'max:500'],
            'answers' => ['sometimes', 'nullable', 'array'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'action_items' => ['sometimes', 'nullable', 'array'],
        ];
    }
}

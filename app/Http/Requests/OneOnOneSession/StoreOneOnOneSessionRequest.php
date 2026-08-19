<?php

namespace App\Http\Requests\OneOnOneSession;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOneOnOneSessionRequest extends FormRequest
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
            'person_id' => ['required', 'integer', TenantRule::exists('people')],
            'one_on_one_template_id' => ['nullable', 'integer', TenantRule::exists('one_on_one_templates')],
            'scheduled_for' => ['nullable', 'date'],
            'held_at' => ['nullable', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:50'],
            'sentiment' => ['nullable', 'string', 'max:50'],
            'questions' => ['nullable', 'array'],
            'questions.*' => ['required', 'string', 'max:500'],
            'answers' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'action_items' => ['nullable', 'array'],
        ];
    }
}

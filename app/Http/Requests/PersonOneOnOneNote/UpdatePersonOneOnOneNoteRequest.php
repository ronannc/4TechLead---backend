<?php

namespace App\Http\Requests\PersonOneOnOneNote;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonOneOnOneNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'person_id' => ['sometimes', 'integer', TenantRule::exists('people')],
            'one_on_one_session_id' => ['sometimes', 'nullable', 'integer', TenantRule::exists('one_on_one_sessions')],
            'title' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:open,used,discarded,resolved'],
            'occurred_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}

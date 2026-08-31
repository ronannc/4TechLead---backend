<?php

namespace App\Http\Requests\PersonOneOnOneNote;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonOneOnOneNoteRequest extends FormRequest
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
            'person_id' => ['required', 'integer', TenantRule::exists('people')],
            'one_on_one_session_id' => ['nullable', 'integer', TenantRule::exists('one_on_one_sessions')],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:open,used,discarded,resolved'],
            'occurred_at' => ['nullable', 'date'],
        ];
    }
}

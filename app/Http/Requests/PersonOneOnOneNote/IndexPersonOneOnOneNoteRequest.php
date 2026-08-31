<?php

namespace App\Http\Requests\PersonOneOnOneNote;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexPersonOneOnOneNoteRequest extends FormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'order' => ['sometimes', 'array'],
            'order.*' => ['sometimes', 'string', 'in:asc,desc'],
            'filters.person_id' => ['sometimes', 'integer', TenantRule::exists('people')],
            'filters.one_on_one_session_id' => ['sometimes', 'integer', TenantRule::exists('one_on_one_sessions')],
            'filters.status' => ['sometimes', 'string', 'in:open,used,discarded,resolved'],
        ];
    }
}

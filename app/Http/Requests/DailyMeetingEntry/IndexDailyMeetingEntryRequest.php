<?php

namespace App\Http\Requests\DailyMeetingEntry;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexDailyMeetingEntryRequest extends FormRequest
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
            'order' => ['sometimes', 'array'],
            'order.*' => ['sometimes', 'string', 'in:asc,desc'],
            'filters.team_id' => ['sometimes', 'integer', TenantRule::exists('teams')],
            'filters.person_id' => ['sometimes', 'integer', TenantRule::exists('people')],
            'filters.daily_meeting_id' => ['sometimes', 'integer', TenantRule::exists('daily_meetings')],
        ];
    }
}

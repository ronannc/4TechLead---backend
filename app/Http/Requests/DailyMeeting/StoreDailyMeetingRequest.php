<?php

namespace App\Http\Requests\DailyMeeting;

use App\Enums\DailyAnnotationType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDailyMeetingRequest extends FormRequest
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
     * `speaking_order` and `allotted_seconds` are deliberately absent from the entries payload —
     * both are derived server-side by DailyMeetingStoreService (array index and the meeting's own
     * time_limit_seconds), so the client can't force an incoherent `allotted_seconds` that would make
     * the `status` accessor lie.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'time_limit_seconds' => [
                'required',
                'integer',
                'min:60',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value % 30 !== 0) {
                        $fail('O limite de tempo deve ser em múltiplos de 30 segundos.');
                    }
                },
            ],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.person_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('people', 'id'),
            ],
            'entries.*.actual_seconds' => ['required', 'integer', 'min:0'],
            'annotations' => ['sometimes', 'array'],
            'annotations.*.type' => ['required', Rule::enum(DailyAnnotationType::class)],
            'annotations.*.text' => ['required', 'string', 'max:2000'],
            'annotations.*.person_id' => ['nullable', 'integer', Rule::exists('people', 'id')],
            'annotations.*.resolved' => ['sometimes', 'boolean'],
        ];
    }
}

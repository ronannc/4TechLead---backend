<?php

namespace App\Http\Requests\Okr;

use App\Models\Okr;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateOkrRequest extends FormRequest
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
            'person_id' => ['sometimes', 'integer', 'exists:people,id'],
            'development_plan_id' => ['sometimes', 'nullable', 'integer', 'exists:development_plans,id'],
            'objective' => ['sometimes', 'string', 'max:255'],
            'cycle' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:50'],
            'focus_area' => ['sometimes', 'nullable', 'string', 'max:255'],
            'diagnosis' => ['sometimes', 'nullable', 'string'],
            'evidence_source' => ['sometimes', 'nullable', 'string'],
            'baseline' => ['sometimes', 'nullable', 'string'],
            'target' => ['sometimes', 'nullable', 'string'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date'],
            'confidence' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('start_date') || $validator->errors()->has('end_date')) {
                    return;
                }

                $okr = Okr::query()
                    ->whereKey($this->route('okr'))
                    ->first();

                $startDate = $this->input('start_date', $okr?->start_date);
                $endDate = $this->input('end_date', $okr?->end_date);

                if ($startDate === null || $endDate === null) {
                    return;
                }

                if (CarbonImmutable::parse($endDate)->lt(CarbonImmutable::parse($startDate))) {
                    $validator->errors()->add(
                        'end_date',
                        'The end date field must be a date after or equal to start date.',
                    );
                }
            },
        ];
    }
}

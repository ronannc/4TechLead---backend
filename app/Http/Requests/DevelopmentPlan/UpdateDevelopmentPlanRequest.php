<?php

namespace App\Http\Requests\DevelopmentPlan;

use App\Models\DevelopmentPlan;
use App\Support\TenantRule;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateDevelopmentPlanRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:50'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date'],
            'target_role' => ['sometimes', 'nullable', 'string', 'max:255'],
            'target_seniority' => ['sometimes', 'nullable', 'string', 'max:50'],
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

                $plan = DevelopmentPlan::query()
                    ->whereKey($this->route('development_plan'))
                    ->first();

                $startDate = $this->input('start_date', $plan?->start_date);
                $endDate = $this->input('end_date', $plan?->end_date);

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

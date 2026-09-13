<?php

namespace App\Http\Requests\DeliveryKpi;

use Illuminate\Foundation\Http\FormRequest;

final class IndexDeliveryKpiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isTechLead() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'person_id' => ['nullable', 'integer', 'exists:people,id'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'sprint' => ['nullable', 'string', 'max:255'],
            'minimum_confidence' => ['nullable', 'in:high,medium,low'],
        ];
    }
}

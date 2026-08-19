<?php

namespace App\Http\Requests\DevelopmentPlanItem;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexDevelopmentPlanItemRequest extends FormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'order' => ['sometimes', 'array'],
            'order.*' => ['sometimes', 'string', 'in:asc,desc'],
            'filters.development_plan_id' => ['sometimes', 'integer', TenantRule::exists('development_plans')],
            'filters.status' => ['sometimes', 'string', 'max:50'],
            'filters.competency' => ['sometimes', 'string', 'max:255'],
        ];
    }
}

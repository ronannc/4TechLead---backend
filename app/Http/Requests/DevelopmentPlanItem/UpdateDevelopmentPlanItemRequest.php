<?php

namespace App\Http\Requests\DevelopmentPlanItem;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDevelopmentPlanItemRequest extends FormRequest
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
            'development_plan_id' => ['sometimes', 'integer', TenantRule::exists('development_plans')],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'competency' => ['sometimes', 'nullable', 'string', 'max:255'],
            'evidence' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:50'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

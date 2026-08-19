<?php

namespace App\Http\Requests\DevelopmentPlanItem;

use App\Support\TenantRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDevelopmentPlanItemRequest extends FormRequest
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
            'development_plan_id' => ['required', 'integer', TenantRule::exists('development_plans')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'competency' => ['nullable', 'string', 'max:255'],
            'evidence' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:50'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }
}

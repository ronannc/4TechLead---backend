<?php

namespace App\Http\Requests\IntegrationWebhook;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIntegrationWebhookRequest extends FormRequest
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
        $isMergedPullRequest = $this->input('event_type') === 'pull_request_merged';

        return [
            'event_id' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'external_actor_code' => ['required', 'string', 'max:255'],
            'occurred_at' => ['nullable', 'date'],
            'source_ref' => ['nullable', 'string', 'max:255'],
            'payload' => ['required', 'array'],
            'payload.task_refs' => ['sometimes', 'array'],
            'payload.task_refs.*' => ['string', 'max:100'],
            'payload.pull_request' => [Rule::requiredIf($isMergedPullRequest), 'array'],
            'payload.pull_request.number' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:1'],
            'payload.pull_request.title' => [Rule::requiredIf($isMergedPullRequest), 'string', 'max:255'],
            'payload.pull_request.review_comments_count' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.comments_count' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.ci_failures_count' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.rework_count' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.story_points' => [Rule::requiredIf($isMergedPullRequest), 'numeric', 'min:0'],
            'payload.pull_request.changed_files' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.additions' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.deletions' => [Rule::requiredIf($isMergedPullRequest), 'integer', 'min:0'],
            'payload.pull_request.created_at' => [Rule::requiredIf($isMergedPullRequest), 'date'],
            'payload.pull_request.merged_at' => [Rule::requiredIf($isMergedPullRequest), 'date'],
        ];
    }
}

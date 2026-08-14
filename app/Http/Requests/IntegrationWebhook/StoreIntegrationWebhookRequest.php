<?php

namespace App\Http\Requests\IntegrationWebhook;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
        $eventType = (string) $this->input('event_type');
        $isPullRequestEvent = Str::startsWith($eventType, 'pull_request_');
        $isMergedPullRequest = $eventType === 'pull_request_merged';

        return [
            'event_id' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'external_actor_code' => ['required', 'string', 'max:255'],
            'occurred_at' => ['nullable', 'date'],
            'source_ref' => ['nullable', 'string', 'max:255'],
            'payload' => ['required', 'array'],
            'payload.task_refs' => ['sometimes', 'array'],
            'payload.task_refs.*' => ['string', 'max:100'],
            'payload.pull_request' => [Rule::requiredIf($isPullRequestEvent), 'array'],
            'payload.pull_request.number' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:1'],
            'payload.pull_request.title' => [Rule::requiredIf($isPullRequestEvent), 'string', 'max:255'],
            'payload.pull_request.author' => [Rule::requiredIf($isPullRequestEvent), 'string', 'max:255'],
            'payload.pull_request.head_ref' => [Rule::requiredIf($isPullRequestEvent), 'string', 'max:255'],
            'payload.pull_request.base_ref' => [Rule::requiredIf($isPullRequestEvent), 'string', 'max:255'],
            'payload.pull_request.review_comments_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.comments_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.review_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.unique_reviewer_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.approvals_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.changes_requested_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.ci_failures_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.rework_count' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.story_points' => [Rule::requiredIf($isPullRequestEvent), 'numeric', 'min:0'],
            'payload.pull_request.changed_files' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.additions' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.deletions' => [Rule::requiredIf($isPullRequestEvent), 'integer', 'min:0'],
            'payload.pull_request.created_at' => [Rule::requiredIf($isPullRequestEvent), 'date'],
            'payload.pull_request.closed_at' => [Rule::requiredIf($isPullRequestEvent), 'date'],
            'payload.pull_request.merged_at' => [Rule::requiredIf($isMergedPullRequest), 'nullable', 'date'],
        ];
    }
}

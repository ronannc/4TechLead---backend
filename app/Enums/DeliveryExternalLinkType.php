<?php

namespace App\Enums;

enum DeliveryExternalLinkType: string
{
    case ClickUpTask = 'clickup_task';
    case GitHubPullRequest = 'github_pull_request';
    case GitHubHeadSha = 'github_head_sha';
    case GitHubWorkflowRun = 'github_workflow_run';
    case GitHubDeployment = 'github_deployment';
}

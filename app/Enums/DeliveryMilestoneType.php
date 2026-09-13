<?php

namespace App\Enums;

enum DeliveryMilestoneType: string
{
    case AnalysisStarted = 'analysis_started';
    case DevelopmentStarted = 'development_started';
    case Blocked = 'blocked';
    case Unblocked = 'unblocked';
    case ImplementationCompleted = 'implementation_completed';
    case QaStarted = 'qa_started';
    case QaFailed = 'qa_failed';
    case QaApproved = 'qa_approved';
    case ReadyToPublish = 'ready_to_publish';
    case Published = 'published';
    case Rejected = 'rejected';
    case PullRequestOpened = 'pull_request_opened';
    case ReviewSubmitted = 'review_submitted';
    case CiCompleted = 'ci_completed';
    case PullRequestMerged = 'pull_request_merged';
    case Homologated = 'homologated';
    case DeploymentCompleted = 'deployment_completed';
}

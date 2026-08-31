<?php

use Symfony\Component\HttpFoundation\BinaryFileResponse;

it('publishes a valid OpenAPI document for external integrations', function (): void {
    $path = resource_path('docs/openapi.json');

    expect($path)->toBeFile();

    $document = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

    expect($document)
        ->toHaveKey('openapi', '3.1.0')
        ->and($document['paths'])
        ->toHaveKeys([
            '/auth/register',
            '/auth/login',
            '/auth/accept-person-invitation',
            '/auth/me',
            '/auth/logout',
            '/me/person',
            '/me/development-plans',
            '/integration-systems',
            '/integration-systems/{integrationSystem}/regenerate-token',
            '/people/{person}/invitation',
            '/person-external-identities',
            '/integration-webhooks/{integrationSystem}',
            '/clickup-webhooks',
            '/github-webhooks',
            '/person-delivery-metrics',
        ])
        ->and($document['components']['schemas'])
        ->toHaveKeys([
            'RegisterRequest',
            'LoginRequest',
            'AcceptPersonInvitationRequest',
            'AuthTokenResponse',
            'User',
            'PersonInvitation',
            'IntegrationWebhookRequest',
            'PullRequestPayload',
            'NormalizedWebhookPayload',
            'PersonDeliveryMetric',
        ])
        ->and($document['components']['securitySchemes'])
        ->toHaveKey('sanctumBearer')
        ->and($document['components']['schemas']['MetricType']['enum'])
        ->toContain(
            'annual_quality_average',
            'annual_ci_failure_average',
            'annual_rework_average',
            'annual_pr_size_average',
            'annual_pr_merge_time_average',
            'annual_review_acceptance_rate',
            'annual_ci_success_rate',
        );
});

it('publishes a browser API reference page', function (): void {
    $path = resource_path('docs/index.html');

    expect($path)
        ->toBeFile()
        ->and(file_get_contents($path))
        ->toContain('/docs/openapi')
        ->toContain('@scalar/api-reference');
});

it('serves API documentation through friendly Laravel routes', function (): void {
    $documentationResponse = $this->get('/docs')
        ->assertOk();

    expect($documentationResponse->baseResponse)
        ->toBeInstanceOf(BinaryFileResponse::class)
        ->and($documentationResponse->baseResponse->getFile()->getPathname())
        ->toBe(resource_path('docs/index.html'));

    $response = $this->get('/docs/openapi')
        ->assertOk()
        ->assertJsonPath('openapi', '3.1.0')
        ->assertJsonPath('info.title', '4TechLead Integrations API')
        ->assertJsonPath('servers.0.url', rtrim((string) config('app.url'), '/').'/api/v1')
        ->assertJsonPath('servers.0.description', 'Current application environment')
        ->assertJsonPath('paths./auth/login.post.summary', 'Log in and receive an API token')
        ->assertJsonPath(
            'paths./integration-systems/{integrationSystem}/regenerate-token.post.summary',
            'Regenerate the webhook token for an integration system',
        )
        ->assertJsonPath(
            'paths./person-external-identities.post.summary',
            'Generate an external actor code for a person',
        )
        ->assertJsonMissingPath('components.schemas.StorePersonExternalIdentityRequest.properties.external_code')
        ->assertJsonMissingPath('components.schemas.PersonExternalIdentity.properties.external_username')
        ->assertJsonMissingPath('components.schemas.DeliveryAnalysis')
        ->assertJsonMissingPath('components.schemas.NormalizedWebhookPayload.properties.analysis')
        ->assertJsonPath('components.schemas.AuthTokenResponse.required.1', 'token');

    expect($response->headers->get('content-type'))->toContain('application/json');
});

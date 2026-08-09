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
            '/auth/me',
            '/auth/logout',
            '/integration-systems',
            '/person-external-identities',
            '/integration-webhooks/{integrationSystem}',
            '/person-delivery-metrics',
        ])
        ->and($document['components']['schemas'])
        ->toHaveKeys([
            'RegisterRequest',
            'LoginRequest',
            'AuthTokenResponse',
            'User',
            'IntegrationWebhookRequest',
            'PullRequestPayload',
            'NormalizedWebhookPayload',
            'PersonDeliveryMetric',
        ])
        ->and($document['components']['securitySchemes'])
        ->toHaveKey('sanctumBearer');
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
        ->assertJsonPath('paths./auth/login.post.summary', 'Log in and receive an API token')
        ->assertJsonPath('components.schemas.AuthTokenResponse.required.1', 'token');

    expect($response->headers->get('content-type'))->toContain('application/json');
});

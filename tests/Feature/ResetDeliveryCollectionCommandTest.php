<?php

use App\Models\DeliveryCase;
use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use App\Models\PersonDeliveryMetric;
use App\Models\Tenant;

it('removes only one tenant collection while preserving its integration setup', function (): void {
    $targetTenant = Tenant::factory()->create();
    $otherTenant = Tenant::factory()->create();
    $targetIntegration = IntegrationSystem::factory()->create(['tenant_id' => $targetTenant->id]);
    $otherIntegration = IntegrationSystem::factory()->create(['tenant_id' => $otherTenant->id]);
    $targetPerson = Person::factory()->create(['tenant_id' => $targetTenant->id]);
    $otherPerson = Person::factory()->create(['tenant_id' => $otherTenant->id]);
    $targetEvent = IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $targetTenant->id,
        'integration_system_id' => $targetIntegration->id,
        'person_id' => $targetPerson->id,
    ]);
    $otherEvent = IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $otherTenant->id,
        'integration_system_id' => $otherIntegration->id,
        'person_id' => $otherPerson->id,
    ]);
    DeliveryCase::factory()->create(['tenant_id' => $targetTenant->id]);
    DeliveryCase::factory()->create(['tenant_id' => $otherTenant->id]);
    PersonDeliveryMetric::factory()->create([
        'tenant_id' => $targetTenant->id,
        'person_id' => $targetPerson->id,
        'integration_system_id' => $targetIntegration->id,
        'integration_webhook_event_id' => $targetEvent->id,
    ]);
    PersonDeliveryMetric::factory()->create([
        'tenant_id' => $otherTenant->id,
        'person_id' => $otherPerson->id,
        'integration_system_id' => $otherIntegration->id,
        'integration_webhook_event_id' => $otherEvent->id,
    ]);

    $this->artisan('delivery-kpis:reset-collection', [
        '--tenant' => $targetTenant->id,
        '--force' => true,
    ])->assertSuccessful();

    expect(DeliveryCase::withoutGlobalScopes()->where('tenant_id', $targetTenant->id)->count())->toBe(0)
        ->and(IntegrationWebhookEvent::withTrashed()->withoutGlobalScopes()->where('tenant_id', $targetTenant->id)->count())->toBe(0)
        ->and(PersonDeliveryMetric::withoutGlobalScopes()->where('tenant_id', $targetTenant->id)->count())->toBe(0)
        ->and(IntegrationSystem::withoutGlobalScopes()->find($targetIntegration->id))->not->toBeNull()
        ->and(DeliveryCase::withoutGlobalScopes()->where('tenant_id', $otherTenant->id)->count())->toBe(1)
        ->and(IntegrationWebhookEvent::withTrashed()->withoutGlobalScopes()->where('tenant_id', $otherTenant->id)->count())->toBe(1)
        ->and(PersonDeliveryMetric::withoutGlobalScopes()->where('tenant_id', $otherTenant->id)->count())->toBe(1);
});

it('requires an explicit target and destructive confirmation', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create(['tenant_id' => $tenant->id]);
    IntegrationWebhookEvent::factory()->create([
        'tenant_id' => $tenant->id,
        'integration_system_id' => $integration->id,
    ]);

    $this->artisan('delivery-kpis:reset-collection')->assertFailed();
    $this->artisan('delivery-kpis:reset-collection', ['--tenant' => $tenant->id])->assertFailed();

    expect(IntegrationWebhookEvent::withTrashed()->withoutGlobalScopes()->where('tenant_id', $tenant->id)->count())->toBe(1);
});

<?php

use App\Enums\DeliveryStage;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseExternalLink;
use App\Models\DeliveryCaseMilestone;
use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Tenant;
use Illuminate\Console\Command;

it('reprojects stored ClickUp events and can be repeated without duplicates', function (): void {
    $tenant = Tenant::factory()->create();
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'provider' => 'clickup',
    ]);

    IntegrationWebhookEvent::factory()->create([
        'integration_system_id' => $integration->id,
        'tenant_id' => $tenant->id,
        'person_id' => null,
        'event_id' => 'clickup-backfill-1',
        'event_type' => 'taskStatusUpdated',
        'normalized_payload' => [
            'source' => 'clickup',
            'occurred_at' => '2026-09-12T12:00:00Z',
            'task_id' => 'clickup-task-backfill',
            'task_custom_id' => 'DRIE-22000',
            'task_refs' => ['DRIE-22000'],
            'task_name' => 'Entrega histórica',
            'task_stage' => DeliveryStage::InProgress->value,
            'task_assignees' => [],
            'task_assignees_authoritative' => true,
        ],
    ]);

    $this->artisan('delivery-cases:project', ['--chunk' => 1])
        ->expectsOutputToContain('1')
        ->assertSuccessful();

    $deliveryCase = DeliveryCase::query()->sole();

    expect($deliveryCase->task_ref)->toBe('DRIE-22000')
        ->and($deliveryCase->current_stage)->toBe(DeliveryStage::InProgress)
        ->and(DeliveryCaseExternalLink::query()->count())->toBe(1)
        ->and(DeliveryCaseMilestone::query()->count())->toBe(1);

    $deliveryCaseId = $deliveryCase->id;
    $externalLinkIds = DeliveryCaseExternalLink::query()->pluck('id')->all();
    $milestoneIds = DeliveryCaseMilestone::query()->pluck('id')->all();

    $this->artisan('delivery-cases:project', ['--chunk' => 1])
        ->assertSuccessful();

    expect(DeliveryCase::query()->sole()->id)->toBe($deliveryCaseId)
        ->and(DeliveryCaseExternalLink::query()->pluck('id')->all())->toBe($externalLinkIds)
        ->and(DeliveryCaseMilestone::query()->pluck('id')->all())->toBe($milestoneIds);
});

it('honors tenant scope and ignores archived webhook events', function (): void {
    $includedTenant = Tenant::factory()->create();
    $excludedTenant = Tenant::factory()->create();

    foreach ([
        [$includedTenant, 'DRIE-22001', false],
        [$includedTenant, 'DRIE-22002', true],
        [$excludedTenant, 'DRIE-22003', false],
    ] as [$tenant, $taskRef, $archived]) {
        $integration = IntegrationSystem::factory()->create([
            'tenant_id' => $tenant->id,
            'provider' => 'clickup',
        ]);
        $event = IntegrationWebhookEvent::factory()->create([
            'integration_system_id' => $integration->id,
            'tenant_id' => $tenant->id,
            'person_id' => null,
            'event_id' => 'event-'.$taskRef,
            'event_type' => 'taskStatusUpdated',
            'normalized_payload' => [
                'source' => 'clickup',
                'occurred_at' => '2026-09-12T12:00:00Z',
                'task_id' => 'task-'.$taskRef,
                'task_custom_id' => $taskRef,
                'task_refs' => [$taskRef],
                'task_stage' => DeliveryStage::InProgress->value,
                'task_assignees' => [],
                'task_assignees_authoritative' => true,
            ],
        ]);

        if ($archived) {
            $event->delete();
        }
    }

    $this->artisan('delivery-cases:project', [
        '--tenant' => $includedTenant->id,
        '--chunk' => 1,
    ])->assertSuccessful();

    expect(DeliveryCase::query()->withoutGlobalScope('tenant')->pluck('task_ref')->all())
        ->toBe(['DRIE-22001']);
});

it('rejects invalid projection options', function (): void {
    $this->artisan('delivery-cases:project', ['--chunk' => 0])
        ->expectsOutputToContain('A opção --chunk deve ser um número inteiro maior que zero.')
        ->assertExitCode(Command::INVALID);

    $this->artisan('delivery-cases:project', ['--tenant' => 'abc'])
        ->expectsOutputToContain('A opção --tenant deve ser um ID numérico maior que zero.')
        ->assertExitCode(Command::INVALID);
});

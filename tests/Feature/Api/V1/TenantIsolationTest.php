<?php

use App\Enums\ContractType;
use App\Enums\SeniorityLevel;
use App\Models\DevelopmentPlan;
use App\Models\ExternalNotification;
use App\Models\IntegrationSystem;
use App\Models\OneOnOneSession;
use App\Models\Person;
use App\Models\PersonInvitation;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates a separate tenant for each direct tech lead registration', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Lead A',
        'email' => 'lead-a@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertCreated()
        ->assertJsonPath('data.role', 'tech_lead');

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Lead B',
        'email' => 'lead-b@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertCreated()
        ->assertJsonPath('data.role', 'tech_lead');

    $leadA = User::query()->where('email', 'lead-a@example.com')->firstOrFail();
    $leadB = User::query()->where('email', 'lead-b@example.com')->firstOrFail();

    expect($leadA->tenant_id)->not->toBeNull()
        ->and($leadB->tenant_id)->not->toBeNull()
        ->and($leadA->tenant_id)->not->toBe($leadB->tenant_id);
});

it('creates invited member logins inside the invited person tenant', function () {
    $tenant = Tenant::factory()->create();
    $techLead = User::factory()->create(['tenant_id' => $tenant->id]);
    $team = Team::factory()->create(['tenant_id' => $tenant->id]);
    $person = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
        'email' => 'member@example.com',
    ]);
    $token = 'ABC123';

    PersonInvitation::factory()->create([
        'tenant_id' => $tenant->id,
        'person_id' => $person->id,
        'invited_by_user_id' => $techLead->id,
        'email' => 'member@example.com',
        'token_hash' => hash('sha256', $token),
        'expires_at' => now()->addDay(),
    ]);

    $this->postJson('/api/v1/auth/accept-person-invitation', [
        'email' => 'member@example.com',
        'token' => $token,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertCreated()
        ->assertJsonPath('data.role', 'member')
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.tenant_id', $tenant->id);

    $member = User::query()->where('email', 'member@example.com')->firstOrFail();

    expect($member->tenant_id)->toBe($tenant->id)
        ->and($member->person_id)->toBe($person->id);
});

it('isolates tenant owned api data between tech leads', function () {
    [$leadA, $teamA, $personA, $integrationA] = tenantFixture('A');
    [$leadB, $teamB, $personB, $integrationB] = tenantFixture('B');

    DevelopmentPlan::factory()->create([
        'tenant_id' => $leadA->tenant_id,
        'person_id' => $personA->id,
        'title' => 'PDI A',
    ]);
    DevelopmentPlan::factory()->create([
        'tenant_id' => $leadB->tenant_id,
        'person_id' => $personB->id,
        'title' => 'PDI B',
    ]);
    OneOnOneSession::factory()->create([
        'tenant_id' => $leadA->tenant_id,
        'person_id' => $personA->id,
        'title' => '1:1 A',
    ]);
    OneOnOneSession::factory()->create([
        'tenant_id' => $leadB->tenant_id,
        'person_id' => $personB->id,
        'title' => '1:1 B',
    ]);
    ExternalNotification::factory()->create([
        'tenant_id' => $leadA->tenant_id,
        'integration_system_id' => $integrationA->id,
        'event_id' => 'notification-a',
    ]);
    ExternalNotification::factory()->create([
        'tenant_id' => $leadB->tenant_id,
        'integration_system_id' => $integrationB->id,
        'event_id' => 'notification-b',
    ]);

    $this->actingAs($leadA, 'sanctum')
        ->getJson('/api/v1/teams')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $teamA->id);

    $this->actingAs($leadA, 'sanctum')
        ->getJson('/api/v1/people')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $personA->id);

    $this->actingAs($leadA, 'sanctum')
        ->getJson('/api/v1/development-plans')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'PDI A');

    $this->actingAs($leadA, 'sanctum')
        ->getJson('/api/v1/one-on-one-sessions')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', '1:1 A');

    $this->actingAs($leadA, 'sanctum')
        ->getJson('/api/v1/notifications')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.event_id', 'notification-a');

    $this->actingAs($leadA, 'sanctum')
        ->getJson("/api/v1/teams/{$teamB->id}")
        ->assertNotFound();

    $this->actingAs($leadA, 'sanctum')
        ->putJson("/api/v1/people/{$personB->id}", ['name' => 'Cross tenant'])
        ->assertNotFound();

    $this->actingAs($leadA, 'sanctum')
        ->postJson('/api/v1/people', validTenantPersonPayload($teamB->id))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('team_id');
});

it('rejects invitations for an existing user from another tenant', function () {
    [$leadA, , $personA] = tenantFixture('A');
    [$leadB] = tenantFixture('B');
    $personA->update(['email' => $leadB->email]);

    $this->actingAs($leadA, 'sanctum')
        ->postJson("/api/v1/people/{$personA->id}/invitation")
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

/**
 * @return array{User, Team, Person, IntegrationSystem}
 */
function tenantFixture(string $suffix): array
{
    $normalizedSuffix = mb_strtolower($suffix);
    $tenant = Tenant::factory()->create(['name' => "Tenant {$suffix}"]);
    $lead = User::factory()->create([
        'tenant_id' => $tenant->id,
        'email' => "lead-{$normalizedSuffix}@example.com",
        'password' => Hash::make('password123'),
    ]);
    $team = Team::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => "Time {$suffix}",
    ]);
    $person = Person::factory()->create([
        'tenant_id' => $tenant->id,
        'team_id' => $team->id,
        'email' => "person-{$normalizedSuffix}@example.com",
    ]);
    $integration = IntegrationSystem::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => "GitHub {$suffix}",
    ]);

    return [$lead, $team, $person, $integration];
}

function validTenantPersonPayload(int $teamId): array
{
    return [
        'name' => 'Grace Hopper',
        'team_id' => $teamId,
        'position' => 'Software Engineer',
        'contract_type' => ContractType::Clt->value,
        'seniority' => SeniorityLevel::Senior->value,
        'email' => 'grace@example.com',
    ];
}

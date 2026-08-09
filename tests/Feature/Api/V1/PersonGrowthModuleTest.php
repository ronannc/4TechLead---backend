<?php

use App\Models\DevelopmentPlan;
use App\Models\Person;
use App\Models\User;

it('manages one on one templates and sessions with search and pagination', function () {
    $person = Person::factory()->create(['name' => 'Grace Hopper', 'position' => 'Backend Engineer']);

    $templateResponse = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/one-on-one-templates', [
            'title' => '1:1 evolução técnica',
            'description' => 'Template para conversas de carreira.',
            'questions' => [
                'Qual foi sua principal evolução?',
                'Onde você precisa de apoio?',
            ],
            'is_default' => true,
        ])
        ->assertCreated()
        ->assertJsonPath('data.questions.0', 'Qual foi sua principal evolução?');

    $templateId = $templateResponse->json('data.id');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/one-on-one-sessions', [
            'person_id' => $person->id,
            'one_on_one_template_id' => $templateId,
            'scheduled_for' => '2026-08-10',
            'held_at' => '2026-08-10',
            'title' => '1:1 Grace - autonomia',
            'status' => 'completed',
            'sentiment' => 'positive',
            'questions' => ['Como foi a entrega?'],
            'answers' => ['Como foi a entrega?' => 'Com menos dependências.'],
            'notes' => 'Falamos sobre autonomia em incidentes backend.',
            'action_items' => [['title' => 'Registrar decisões técnicas', 'done' => false]],
        ])
        ->assertCreated()
        ->assertJsonPath('data.person_id', $person->id)
        ->assertJsonPath('data.status', 'completed');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/one-on-one-sessions?filters[person_id]={$person->id}&search=incidentes&per_page=5")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', '1:1 Grace - autonomia');
});

it('manages development plans and trackable items', function () {
    $person = Person::factory()->create();

    $planResponse = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/development-plans', [
            'person_id' => $person->id,
            'title' => 'PDI autonomia backend',
            'summary' => 'Evoluir tomada de decisão e comunicação.',
            'status' => 'active',
            'start_date' => '2026-08-01',
            'end_date' => '2026-10-31',
            'target_role' => 'Tech Lead',
            'target_seniority' => 'senior',
            'progress' => 25,
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'PDI autonomia backend');

    $planId = $planResponse->json('data.id');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/development-plan-items', [
            'development_plan_id' => $planId,
            'title' => 'Conduzir desenho técnico',
            'description' => 'Planejar solução antes de implementar.',
            'competency' => 'Arquitetura',
            'evidence' => 'RFC curto e PR entregue.',
            'status' => 'doing',
            'due_date' => '2026-09-15',
            'progress' => 40,
        ])
        ->assertCreated()
        ->assertJsonPath('data.competency', 'Arquitetura');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/development-plans?filters[person_id]={$person->id}&search=autonomia")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.items.0.title', 'Conduzir desenho técnico');
});

it('suggests one on one questions, pdi actions, and kpis for a person', function () {
    $person = Person::factory()->create([
        'name' => 'Linus Torvalds',
        'position' => 'Frontend Engineer',
        'seniority' => 'pleno',
    ]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/people/{$person->id}/growth-suggestions?focus_area=acessibilidade")
        ->assertOk()
        ->assertJsonPath('data.source', 'deterministic')
        ->assertJsonCount(5, 'data.one_on_one_questions')
        ->assertJsonPath('data.kpi_suggestions.0.focus_area', 'acessibilidade')
        ->assertJsonPath('data.kpi_suggestions.0.metrics.0', 'annual_quality_average');
});

it('rejects invalid progress values', function () {
    $person = Person::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/development-plans', [
            'person_id' => $person->id,
            'title' => 'PDI inválido',
            'progress' => 120,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('progress');
});

it('rejects development plan partial updates that would invert the date range', function () {
    $plan = DevelopmentPlan::factory()->create([
        'start_date' => '2026-08-10',
        'end_date' => '2026-09-10',
    ]);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/development-plans/{$plan->id}", [
            'end_date' => '2026-08-01',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/development-plans/{$plan->id}", [
            'start_date' => '2026-09-20',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');
});

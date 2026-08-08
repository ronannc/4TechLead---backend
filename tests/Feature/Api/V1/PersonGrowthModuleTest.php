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

it('manages okrs and measurable key results', function () {
    $person = Person::factory()->create();
    $plan = DevelopmentPlan::factory()->create(['person_id' => $person->id]);

    $okrResponse = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/okrs', [
            'person_id' => $person->id,
            'development_plan_id' => $plan->id,
            'objective' => 'Aumentar autonomia técnica nas entregas críticas',
            'cycle' => '2026-Q3',
            'status' => 'active',
            'focus_area' => 'Autonomia',
            'diagnosis' => 'Ainda depende de validação frequente.',
            'evidence_source' => 'PRs, incidentes e decisões registradas.',
            'baseline' => 'Decisões críticas sempre acompanhadas.',
            'target' => 'Decisões de médio risco conduzidas com checkpoint.',
            'confidence' => 70,
            'progress' => 15,
        ])
        ->assertCreated()
        ->assertJsonPath('data.objective', 'Aumentar autonomia técnica nas entregas críticas');

    $okrId = $okrResponse->json('data.id');

    $keyResultResponse = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/v1/okr-key-results', [
            'okr_id' => $okrId,
            'title' => 'Concluir ações do PDI',
            'metric_name' => 'Ações concluídas',
            'data_source' => 'tasks',
            'initial_value' => 0,
            'current_value' => 1,
            'target_value' => 4,
            'unit' => 'ações',
            'confidence' => 65,
            'status' => 'doing',
            'progress' => 25,
        ])
        ->assertCreated()
        ->assertJsonPath('data.metric_name', 'Ações concluídas')
        ->assertJsonPath('data.data_source', 'tasks');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/okr-key-results/{$keyResultResponse->json('data.id')}", [
            'current_value' => 2,
            'data_source' => 'pull_requests',
            'progress' => 50,
        ])
        ->assertOk()
        ->assertJsonPath('data.current_value', '2.00')
        ->assertJsonPath('data.data_source', 'pull_requests')
        ->assertJsonPath('data.progress', 50);

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/v1/okrs?filters[person_id]={$person->id}&search=autonomia")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.key_results.0.title', 'Concluir ações do PDI');
});

it('suggests one on one questions, pdi actions, and okrs for a person', function () {
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
        ->assertJsonPath('data.okr_suggestions.0.focus_area', 'acessibilidade');
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

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PersonGrowthSuggestionController extends Controller
{
    public function __invoke(Request $request, Person $person): JsonResponse
    {
        $this->authorize('view', $person);

        $data = $request->validate([
            'focus_area' => ['sometimes', 'nullable', 'string', 'max:255'],
            'context' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);

        $focusArea = $data['focus_area'] ?? $this->defaultFocusArea($person);
        $context = $data['context'] ?? null;
        $needsAutonomy = in_array($person->seniority?->value, ['estagiario', 'junior', 'pleno'], true);

        return response()->json([
            'data' => [
                'one_on_one_questions' => $this->oneOnOneQuestions($person, $focusArea, $needsAutonomy),
                'pdi_suggestions' => $this->pdiSuggestions($person, $focusArea, $needsAutonomy),
                'kpi_suggestions' => $this->kpiSuggestions($person, $focusArea, $context, $needsAutonomy),
                'source' => 'deterministic',
            ],
        ]);
    }

    private function defaultFocusArea(Person $person): string
    {
        $position = strtolower((string) $person->position);

        if (str_contains($position, 'front')) {
            return 'qualidade e autonomia em frontend';
        }

        if (str_contains($position, 'back')) {
            return 'confiabilidade e autonomia em backend';
        }

        return 'autonomia, comunicação e impacto técnico';
    }

    /**
     * @return array<int, string>
     */
    private function oneOnOneQuestions(Person $person, string $focusArea, bool $needsAutonomy): array
    {
        return [
            "Qual situação recente mostra evolução em {$focusArea}?",
            $needsAutonomy
                ? 'Onde você ainda precisou de apoio para decidir o próximo passo?'
                : 'Que decisão técnica você poderia multiplicar para outras pessoas do time?',
            'Qual evidência podemos revisar juntos até o próximo 1:1?',
            'O que está reduzindo sua energia, clareza ou foco neste ciclo?',
            "Que apoio do tech lead ajudaria {$person->name} a evoluir mais rápido?",
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function pdiSuggestions(Person $person, string $focusArea, bool $needsAutonomy): array
    {
        return [
            [
                'title' => $needsAutonomy
                    ? 'Conduzir uma entrega com plano técnico próprio'
                    : 'Liderar uma decisão técnica com impacto no time',
                'competency' => $needsAutonomy ? 'Autonomia' : 'Liderança técnica',
                'evidence' => 'PRs, decisões registradas, feedbacks de review e resultado da entrega.',
            ],
            [
                'title' => "Evoluir em {$focusArea}",
                'competency' => 'Qualidade técnica',
                'evidence' => 'Comparar baseline atual com critérios objetivos definidos no PDI.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function kpiSuggestions(Person $person, string $focusArea, ?string $context, bool $needsAutonomy): array
    {
        $contextHint = $context ? " considerando {$context}" : '';

        return [
            [
                'title' => "Acompanhar evolução de {$person->name} em {$focusArea}{$contextHint}",
                'focus_area' => $focusArea,
                'diagnosis' => $needsAutonomy
                    ? 'Há oportunidade de reduzir dependência de direcionamento frequente.'
                    : 'Há oportunidade de ampliar impacto além das próprias entregas.',
                'metrics' => [
                    'annual_quality_average',
                    'annual_rework_average',
                    'annual_pr_merge_time_average',
                ],
            ],
        ];
    }
}

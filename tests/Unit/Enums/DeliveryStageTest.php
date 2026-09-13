<?php

use App\Enums\DeliveryStage;

it('maps the clickup kanban statuses to canonical delivery stages', function (string $status, DeliveryStage $stage): void {
    expect(DeliveryStage::fromClickUpStatus($status))->toBe($stage);
})->with([
    'pending' => ['Pendente', DeliveryStage::Pending],
    'analysis' => ['Em análise', DeliveryStage::Analysis],
    'in progress' => ['Fazendo', DeliveryStage::InProgress],
    'blocked' => ['Impedimento', DeliveryStage::Blocked],
    'ready for qa' => ['Pronto para teste', DeliveryStage::ReadyForQa],
    'in qa' => ['Em teste de QA', DeliveryStage::InQa],
    'qa alias already observed' => ['teste de qualidade', DeliveryStage::InQa],
    'qa failed' => ['Reprovado pelo QA', DeliveryStage::QaFailed],
    'ready to publish' => ['Pronto para publicar', DeliveryStage::ReadyToPublish],
    'published' => ['Publicado', DeliveryStage::Published],
    'rejected' => ['Rejeitado', DeliveryStage::Rejected],
]);

it('normalizes spacing casing and accents from clickup', function (): void {
    expect(DeliveryStage::fromClickUpStatus('  EM   ANÁLISE  '))->toBe(DeliveryStage::Analysis);
});

it('does not invent a stage for unknown or missing statuses', function (mixed $status): void {
    expect(DeliveryStage::fromClickUpStatus($status))->toBeNull();
})->with([
    'unknown text' => 'done',
    'empty text' => '',
    'null' => null,
    'number' => 1,
]);

it('only treats published as a completed delivery', function (): void {
    expect(DeliveryStage::Published->isDelivery())->toBeTrue()
        ->and(DeliveryStage::ReadyToPublish->isDelivery())->toBeFalse()
        ->and(DeliveryStage::Rejected->isDelivery())->toBeFalse()
        ->and(DeliveryStage::Rejected->isCancellation())->toBeTrue();
});

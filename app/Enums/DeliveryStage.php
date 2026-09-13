<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum DeliveryStage: string
{
    case Pending = 'pending';
    case Analysis = 'analysis';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case ReadyForQa = 'ready_for_qa';
    case InQa = 'in_qa';
    case QaFailed = 'qa_failed';
    case ReadyToPublish = 'ready_to_publish';
    case Published = 'published';
    case Rejected = 'rejected';

    public static function fromClickUpStatus(mixed $status): ?self
    {
        if (! is_string($status) || trim($status) === '') {
            return null;
        }

        $normalizedStatus = Str::of($status)
            ->ascii()
            ->lower()
            ->squish()
            ->value();

        return match ($normalizedStatus) {
            'pendente' => self::Pending,
            'em analise' => self::Analysis,
            'fazendo' => self::InProgress,
            'impedimento' => self::Blocked,
            'pronto para teste' => self::ReadyForQa,
            'em teste de qa', 'teste de qa', 'teste de qualidade' => self::InQa,
            'reprovado pelo qa' => self::QaFailed,
            'pronto para publicar' => self::ReadyToPublish,
            'publicado' => self::Published,
            'rejeitado' => self::Rejected,
            default => null,
        };
    }

    public function isDelivery(): bool
    {
        return $this === self::Published;
    }

    public function isCancellation(): bool
    {
        return $this === self::Rejected;
    }
}

<?php

namespace App\Services;

use App\Models\ExternalNotification;
use App\Models\IntegrationSystem;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ExternalNotificationIngestService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function ingest(IntegrationSystem $integrationSystem, string $token, array $data): ExternalNotification
    {
        $this->assertCanReceive($integrationSystem, $token);

        return DB::transaction(function () use ($integrationSystem, $data): ExternalNotification {
            $metadata = (array) ($data['metadata'] ?? []);

            if (($data['occurred_at'] ?? null) !== null) {
                $metadata['occurred_at'] = $data['occurred_at'];
            }

            $notification = ExternalNotification::query()->createOrFirst(
                [
                    'integration_system_id' => $integrationSystem->id,
                    'event_id' => $data['event_id'],
                ],
                [
                    'title' => $data['title'],
                    'message' => $data['message'] ?? null,
                    'type' => $data['type'] ?? null,
                    'severity' => $data['severity'] ?? 'info',
                    'source_ref' => $data['source_ref'] ?? null,
                    'payload' => $data['payload'] ?? null,
                    'metadata' => $metadata === [] ? null : $metadata,
                    'received_at' => now(),
                ],
            );

            if ($notification->wasRecentlyCreated) {
                $integrationSystem->forceFill(['last_received_at' => now()])->save();
            }

            return $notification->refresh();
        });
    }

    protected function assertCanReceive(IntegrationSystem $integrationSystem, string $token): void
    {
        if (! $integrationSystem->active) {
            throw new AccessDeniedHttpException('Integration is inactive.');
        }

        if ($token === '' || ! hash_equals($integrationSystem->token_hash, hash('sha256', $token))) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid integration token.');
        }
    }
}

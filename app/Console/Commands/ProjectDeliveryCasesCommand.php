<?php

namespace App\Console\Commands;

use App\Models\IntegrationWebhookEvent;
use App\Services\DeliveryCaseProjector;
use App\Services\GitHubDeliveryCaseProjector;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

final class ProjectDeliveryCasesCommand extends Command
{
    /** @var string */
    protected $signature = 'delivery-cases:project
        {--tenant= : Limita a reprojeção ao ID de um tenant}
        {--chunk=200 : Quantidade de eventos processada por lote}';

    /** @var string */
    protected $description = 'Reprojeta webhooks armazenados no modelo canônico de entregas';

    public function handle(
        DeliveryCaseProjector $clickUpProjector,
    ): int {
        $chunkSize = filter_var($this->option('chunk'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($chunkSize === false) {
            $this->components->error('A opção --chunk deve ser um número inteiro maior que zero.');

            return self::INVALID;
        }

        $tenantId = $this->tenantId();

        if ($tenantId === false) {
            $this->components->error('A opção --tenant deve ser um ID numérico maior que zero.');

            return self::INVALID;
        }

        $processed = 0;
        $projected = 0;
        $ignored = 0;
        $failures = 0;

        $query = IntegrationWebhookEvent::query()
            ->withoutGlobalScope('tenant')
            ->where(function (Builder $query): void {
                $query
                    ->where('normalized_payload->source', 'clickup')
                    ->orWhere('normalized_payload->source', 'github');
            });

        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }

        $query->chunkById(
            $chunkSize,
            function (Collection $events) use (
                $clickUpProjector,
                &$processed,
                &$projected,
                &$ignored,
                &$failures,
            ): void {
                foreach ($events as $event) {
                    $processed++;

                    try {
                        $deliveryCase = match (data_get($event->normalized_payload, 'source')) {
                            'clickup' => $clickUpProjector->project($event),
                            'github' => app(GitHubDeliveryCaseProjector::class)->project($event),
                            default => null,
                        };

                        if ($deliveryCase === null) {
                            $ignored++;

                            continue;
                        }

                        $projected++;
                    } catch (Throwable $exception) {
                        $failures++;
                        $this->components->warn(sprintf(
                            'Evento %s (%s) falhou: %s',
                            $event->id,
                            $event->event_id,
                            $exception->getMessage(),
                        ));
                    }
                }
            },
            'id',
        );

        $this->table(
            ['Processados', 'Projetados', 'Ignorados', 'Falhas'],
            [[$processed, $projected, $ignored, $failures]],
        );

        if ($failures > 0) {
            $this->components->error('A reprojeção terminou com falhas.');

            return self::FAILURE;
        }

        $this->components->info('Reprojeção concluída.');

        return self::SUCCESS;
    }

    private function tenantId(): int|false|null
    {
        $tenantOption = $this->option('tenant');

        if ($tenantOption === null || $tenantOption === '') {
            return null;
        }

        return filter_var($tenantOption, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
    }
}

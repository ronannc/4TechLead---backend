<?php

namespace App\Console\Commands;

use App\Models\DeliveryCase;
use App\Models\IntegrationWebhookEvent;
use App\Models\PersonDeliveryMetric;
use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('delivery-kpis:reset-collection
    {--tenant= : ID do tenant cuja coleta será reiniciada}
    {--all : Reinicia a coleta de todos os tenants}
    {--force : Confirma a remoção permanente dos dados de coleta}')]
#[Description('Remove eventos, projeções e métricas para iniciar uma nova coleta de entregas.')]
final class ResetDeliveryCollectionCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantIds = $this->tenantIds();

        if ($tenantIds === null) {
            return self::FAILURE;
        }

        if (! $this->option('force')) {
            $this->error('A operação é destrutiva. Execute novamente com --force.');

            return self::FAILURE;
        }

        $totals = [
            'delivery_cases' => 0,
            'delivery_metrics' => 0,
            'webhook_events' => 0,
        ];

        foreach ($tenantIds as $tenantId) {
            $deleted = DB::transaction(function () use ($tenantId): array {
                $deliveryCases = DeliveryCase::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->delete();
                $deliveryMetrics = PersonDeliveryMetric::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->delete();
                $webhookEvents = IntegrationWebhookEvent::withTrashed()
                    ->withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->forceDelete();

                return [
                    'delivery_cases' => $deliveryCases,
                    'delivery_metrics' => $deliveryMetrics,
                    'webhook_events' => $webhookEvents,
                ];
            });

            foreach ($totals as $key => $total) {
                $totals[$key] = $total + $deleted[$key];
            }
        }

        $this->table(
            ['Dado removido', 'Quantidade'],
            [
                ['Casos de entrega', $totals['delivery_cases']],
                ['Métricas legadas', $totals['delivery_metrics']],
                ['Eventos de webhook', $totals['webhook_events']],
            ],
        );
        $this->info('Nova coleta de entregas pronta para iniciar. Configurações e mapeamentos foram preservados.');

        return self::SUCCESS;
    }

    /**
     * @return list<int>|null
     */
    private function tenantIds(): ?array
    {
        $tenantId = $this->option('tenant');

        if ((is_int($tenantId) || is_string($tenantId)) && ctype_digit((string) $tenantId)) {
            if (! Tenant::query()->whereKey((int) $tenantId)->exists()) {
                $this->error("Tenant {$tenantId} não encontrado.");

                return null;
            }

            return [(int) $tenantId];
        }

        if ($this->option('all')) {
            /** @var list<int> $tenantIds */
            $tenantIds = Tenant::query()->pluck('id')->map(static fn (int $id): int => $id)->all();

            return $tenantIds;
        }

        $this->error('Informe --tenant=<id> ou --all.');

        return null;
    }
}

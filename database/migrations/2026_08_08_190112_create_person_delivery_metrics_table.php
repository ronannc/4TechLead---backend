<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('person_delivery_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('integration_system_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('integration_webhook_event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('metric_type');
            $table->decimal('metric_value', 10, 2);
            $table->string('unit')->nullable();
            $table->string('source_ref')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['person_id', 'metric_type', 'occurred_at']);
            $table->unique(
                ['integration_webhook_event_id', 'metric_type'],
                'delivery_metric_event_type_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_delivery_metrics');
    }
};

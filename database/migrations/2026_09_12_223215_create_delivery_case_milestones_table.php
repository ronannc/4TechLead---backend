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
        Schema::create('delivery_case_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('integration_webhook_event_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('milestone_type');
            $table->string('semantic_key');
            $table->string('source_provider');
            $table->string('source_ref')->nullable();
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(
                ['delivery_case_id', 'semantic_key'],
                'delivery_case_milestone_semantic_unique',
            );
            $table->index(
                ['tenant_id', 'milestone_type', 'occurred_at'],
                'delivery_case_milestone_timeline_index',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_case_milestones');
    }
};

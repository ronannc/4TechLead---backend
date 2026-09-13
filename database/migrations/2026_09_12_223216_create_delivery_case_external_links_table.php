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
        Schema::create('delivery_case_external_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('integration_system_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('integration_webhook_event_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('link_type');
            $table->string('external_id');
            $table->string('source_ref')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(
                ['tenant_id', 'link_type', 'external_id'],
                'delivery_case_external_link_unique',
            );
            $table->index(
                ['delivery_case_id', 'link_type'],
                'delivery_case_external_link_lookup_index',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_case_external_links');
    }
};

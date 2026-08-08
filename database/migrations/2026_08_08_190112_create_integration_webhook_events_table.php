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
        Schema::create('integration_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_system_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->string('event_id');
            $table->string('event_type');
            $table->string('external_actor_code')->nullable();
            $table->string('status')->default('received');
            $table->text('failure_reason')->nullable();
            $table->json('payload');
            $table->json('normalized_payload')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->unique(['integration_system_id', 'event_id']);
            $table->index(['person_id', 'event_type', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_webhook_events');
    }
};

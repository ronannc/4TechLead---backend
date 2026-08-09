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
        Schema::create('external_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('integration_system_id')->constrained()->cascadeOnDelete();
            $table->string('event_id');
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->nullable();
            $table->string('severity', 32)->default('info');
            $table->string('source_ref')->nullable();
            $table->json('payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->unique(['integration_system_id', 'event_id']);
            $table->index(['severity', 'received_at']);
            $table->index(['type', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_notifications');
    }
};

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
        Schema::create('delivery_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('task_ref');
            $table->string('title')->nullable();
            $table->string('current_stage')->nullable();
            $table->string('sprint_ref')->nullable();
            $table->decimal('story_points', 8, 2)->nullable();
            $table->timestamp('first_seen_at');
            $table->timestamp('last_activity_at')->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamp('canceled_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['tenant_id', 'task_ref']);
            $table->index(['tenant_id', 'current_stage', 'last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_cases');
    }
};

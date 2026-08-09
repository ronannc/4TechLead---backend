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
        Schema::create('daily_meeting_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_meeting_id')->constrained()->cascadeOnDelete();
            // Denormalized from the parent meeting so Filterable::scopeFilter (plain column `where`,
            // no joins) can support `filters[team_id]` directly on this resource.
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            // Historical record — never cascade-delete a person's speaking history.
            $table->foreignId('person_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('speaking_order');
            $table->unsignedInteger('allotted_seconds');
            $table->unsignedInteger('actual_seconds');
            $table->timestamps();

            $table->index(['team_id', 'created_at']);
            $table->index(['person_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_meeting_entries');
    }
};

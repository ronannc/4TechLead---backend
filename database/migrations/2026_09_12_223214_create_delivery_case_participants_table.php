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
        Schema::create('delivery_case_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->restrictOnDelete();
            $table->string('role');
            $table->string('source_provider');
            $table->string('source_ref')->nullable();
            $table->string('confidence');
            $table->timestamp('valid_from');
            $table->timestamp('valid_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(
                ['delivery_case_id', 'person_id', 'role', 'valid_from'],
                'delivery_case_participant_interval_unique',
            );
            $table->index(
                ['tenant_id', 'person_id', 'role', 'valid_to'],
                'delivery_case_participant_lookup_index',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_case_participants');
    }
};

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
        Schema::create('one_on_one_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('one_on_one_template_id')->nullable()->constrained()->nullOnDelete();
            $table->date('scheduled_for')->nullable();
            $table->date('held_at')->nullable();
            $table->string('title');
            $table->string('status')->default('draft');
            $table->string('sentiment')->nullable();
            $table->json('questions')->nullable();
            $table->json('answers')->nullable();
            $table->text('notes')->nullable();
            $table->json('action_items')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('one_on_one_sessions');
    }
};

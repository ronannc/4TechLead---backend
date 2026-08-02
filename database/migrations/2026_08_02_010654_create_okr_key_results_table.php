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
        Schema::create('okr_key_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('metric_name')->nullable();
            $table->decimal('initial_value', 10, 2)->nullable();
            $table->decimal('current_value', 10, 2)->nullable();
            $table->decimal('target_value', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->unsignedTinyInteger('confidence')->default(50);
            $table->string('status')->default('todo');
            $table->date('due_date')->nullable();
            $table->text('evidence')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('okr_key_results');
    }
};

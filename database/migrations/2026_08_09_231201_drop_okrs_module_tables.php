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
        Schema::dropIfExists('okr_key_results');
        Schema::dropIfExists('okrs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('okrs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('development_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('objective');
            $table->string('cycle')->nullable();
            $table->string('status')->default('draft');
            $table->string('focus_area')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('evidence_source')->nullable();
            $table->text('baseline')->nullable();
            $table->text('target')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('confidence')->default(50);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });

        Schema::create('okr_key_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('okr_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('metric_name')->nullable();
            $table->string('data_source')->default('manual');
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
};

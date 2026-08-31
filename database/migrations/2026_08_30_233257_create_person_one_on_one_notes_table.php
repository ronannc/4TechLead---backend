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
        Schema::create('person_one_on_one_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('one_on_one_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('status')->default('open');
            $table->date('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'person_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_one_on_one_notes');
    }
};

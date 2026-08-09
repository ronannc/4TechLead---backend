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
        Schema::create('daily_meeting_annotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->text('text');
            $table->boolean('resolved')->default(false);
            $table->timestamps();

            $table->index(['daily_meeting_id', 'type']);
            $table->index(['person_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_meeting_annotations');
    }
};

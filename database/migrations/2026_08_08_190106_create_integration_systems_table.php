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
        Schema::create('integration_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->text('description')->nullable();
            $table->string('token_hash');
            $table->string('token_prefix', 12);
            $table->boolean('active')->default(true);
            $table->timestamp('last_received_at')->nullable();
            $table->timestamps();

            $table->index(['provider', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_systems');
    }
};

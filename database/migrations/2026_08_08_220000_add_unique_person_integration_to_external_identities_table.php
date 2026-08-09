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
        Schema::table('person_external_identities', function (Blueprint $table): void {
            $table->unique(['integration_system_id', 'person_id'], 'pei_integration_person_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('person_external_identities', function (Blueprint $table): void {
            $table->dropUnique('pei_integration_person_unique');
        });
    }
};

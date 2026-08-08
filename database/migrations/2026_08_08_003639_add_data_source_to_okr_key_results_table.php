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
        Schema::table('okr_key_results', function (Blueprint $table): void {
            $table->string('data_source')->default('manual')->after('metric_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('okr_key_results', function (Blueprint $table): void {
            $table->dropColumn('data_source');
        });
    }
};

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
        Schema::table('one_on_one_sessions', function (Blueprint $table): void {
            $table->json('document_snapshot')->nullable()->after('one_on_one_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_on_one_sessions', function (Blueprint $table): void {
            $table->dropColumn('document_snapshot');
        });
    }
};

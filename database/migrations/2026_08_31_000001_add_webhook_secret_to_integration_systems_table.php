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
        Schema::table('integration_systems', function (Blueprint $table): void {
            $table->text('webhook_secret')->nullable()->after('token_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_systems', function (Blueprint $table): void {
            $table->dropColumn('webhook_secret');
        });
    }
};

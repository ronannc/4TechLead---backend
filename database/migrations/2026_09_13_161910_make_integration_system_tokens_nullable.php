<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('integration_systems', function (Blueprint $table): void {
            $table->string('token_hash')->nullable()->change();
            $table->string('token_prefix', 12)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('integration_systems')
            ->whereNull('token_hash')
            ->update([
                'token_hash' => hash('sha256', Str::random(64)),
                'token_prefix' => 'revogado',
            ]);

        Schema::table('integration_systems', function (Blueprint $table): void {
            $table->string('token_hash')->nullable(false)->change();
            $table->string('token_prefix', 12)->nullable(false)->change();
        });
    }
};

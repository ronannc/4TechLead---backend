<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('integration_webhook_events', function (Blueprint $table): void {
            $table->dropForeign(['integration_system_id']);
            $table->foreignId('integration_system_id')->nullable()->change();
            $table->foreign('integration_system_id')
                ->references('id')
                ->on('integration_systems')
                ->nullOnDelete();
        });

        Schema::table('person_external_identities', function (Blueprint $table): void {
            $table->dropForeign(['integration_system_id']);
            $table->foreignId('integration_system_id')->nullable()->change();
            $table->foreign('integration_system_id')
                ->references('id')
                ->on('integration_systems')
                ->nullOnDelete();
        });

        Schema::table('external_notifications', function (Blueprint $table): void {
            $table->dropForeign(['integration_system_id']);
            $table->foreignId('integration_system_id')->nullable()->change();
            $table->foreign('integration_system_id')
                ->references('id')
                ->on('integration_systems')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([
            'integration_webhook_events',
            'person_external_identities',
            'external_notifications',
        ] as $tableName) {
            if (DB::table($tableName)->whereNull('integration_system_id')->exists()) {
                throw new RuntimeException(
                    'Cannot roll back integration history preservation while detached records exist.'
                );
            }
        }

        Schema::table('integration_webhook_events', function (Blueprint $table): void {
            $table->dropForeign(['integration_system_id']);
            $table->foreignId('integration_system_id')->nullable(false)->change();
            $table->foreign('integration_system_id')
                ->references('id')
                ->on('integration_systems')
                ->cascadeOnDelete();
        });

        Schema::table('person_external_identities', function (Blueprint $table): void {
            $table->dropForeign(['integration_system_id']);
            $table->foreignId('integration_system_id')->nullable(false)->change();
            $table->foreign('integration_system_id')
                ->references('id')
                ->on('integration_systems')
                ->cascadeOnDelete();
        });

        Schema::table('external_notifications', function (Blueprint $table): void {
            $table->dropForeign(['integration_system_id']);
            $table->foreignId('integration_system_id')->nullable(false)->change();
            $table->foreign('integration_system_id')
                ->references('id')
                ->on('integration_systems')
                ->cascadeOnDelete();
        });
    }
};

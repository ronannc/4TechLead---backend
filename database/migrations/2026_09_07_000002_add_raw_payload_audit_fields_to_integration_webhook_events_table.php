<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_webhook_events', function (Blueprint $table): void {
            $table->string('payload_hash', 64)->nullable()->after('payload');
            $table->unsignedInteger('payload_size_bytes')->nullable()->after('payload_hash');
        });
    }

    public function down(): void
    {
        Schema::table('integration_webhook_events', function (Blueprint $table): void {
            $table->dropColumn(['payload_hash', 'payload_size_bytes']);
        });
    }
};

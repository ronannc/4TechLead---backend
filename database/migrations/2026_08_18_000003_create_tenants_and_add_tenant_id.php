<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = [
        'users',
        'teams',
        'people',
        'daily_meetings',
        'daily_meeting_entries',
        'daily_meeting_annotations',
        'one_on_one_templates',
        'one_on_one_sessions',
        'development_plans',
        'development_plan_items',
        'integration_systems',
        'integration_webhook_events',
        'person_delivery_metrics',
        'person_external_identities',
        'external_notifications',
        'person_invitations',
    ];

    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            });
        }

        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'Tenant inicial',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->tables as $tableName) {
            DB::table($tableName)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('tenant_id');
            });
        }

        Schema::dropIfExists('tenants');
    }
};

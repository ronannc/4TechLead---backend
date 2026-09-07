<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->string('github_username', 39)->nullable()->after('phone');
            $table->string('clickup_user_id')->nullable()->after('github_username');
            $table->unique(['tenant_id', 'github_username'], 'people_tenant_github_username_unique');
            $table->unique(['tenant_id', 'clickup_user_id'], 'people_tenant_clickup_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->dropUnique('people_tenant_github_username_unique');
            $table->dropUnique('people_tenant_clickup_user_id_unique');
            $table->dropColumn(['github_username', 'clickup_user_id']);
        });
    }
};

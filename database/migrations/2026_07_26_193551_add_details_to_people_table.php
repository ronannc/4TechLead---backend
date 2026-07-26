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
        Schema::table('people', function (Blueprint $table) {
            $table->date('birth_date')->nullable();
            $table->string('position')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('seniority')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date',
                'position',
                'contract_type',
                'email',
                'phone',
                'admission_date',
                'seniority',
            ]);
        });
    }
};

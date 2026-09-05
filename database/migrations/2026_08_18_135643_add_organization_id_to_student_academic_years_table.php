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
        Schema::table('student_academic_years', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->after('academic_year_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            $table->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic_years', function (Blueprint $table) {
            $table->dropForeign([
                'organization_id',
            ]);

            $table->dropIndex([
                'organization_id',
            ]);

            $table->dropColumn('organization_id');
        });
    }
};

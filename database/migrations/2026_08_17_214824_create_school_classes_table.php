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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Unit / organisasi pemilik kelas
            |--------------------------------------------------------------------------
            */

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Tahun ajaran
            |--------------------------------------------------------------------------
            */

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Nama kelas
            |--------------------------------------------------------------------------
            */

            $table->string('name', 100);

            /*
            |--------------------------------------------------------------------------
            | Status kelas
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Nama kelas tidak boleh duplikat dalam satu unit
            | pada tahun ajaran yang sama.
            |--------------------------------------------------------------------------
            */

            $table->unique(
                [
                    'organization_id',
                    'academic_year_id',
                    'name',
                ],
                'school_classes_org_year_name_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};

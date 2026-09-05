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
        Schema::create('student_academic_years', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Siswa
            |--------------------------------------------------------------------------
            */

            $table->foreignId('student_id')
                ->constrained('students')
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
            | Kelas
            |--------------------------------------------------------------------------
            |
            | Nullable karena siswa bisa dicatat terlebih dahulu
            | sebelum ditempatkan ke kelas.
            |
            */

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Status siswa pada tahun ajaran
            |--------------------------------------------------------------------------
            */

            $table->string('status', 30)
                ->default('active');

            /*
            |--------------------------------------------------------------------------
            | Periode siswa pada tahun ajaran
            |--------------------------------------------------------------------------
            */

            $table->date('started_at')
                ->nullable();

            $table->date('ended_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Satu siswa hanya boleh memiliki satu record
            | dalam satu tahun ajaran.
            |--------------------------------------------------------------------------
            */

            $table->unique(
                [
                    'student_id',
                    'academic_year_id',
                ],
                'student_academic_year_unique'
            );

            /*
            |--------------------------------------------------------------------------
            | Index tambahan
            |--------------------------------------------------------------------------
            */

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_years');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_bills', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Siswa pada tahun akademik tertentu
            |--------------------------------------------------------------------------
            */

            $table->foreignId(
                'student_academic_year_id'
            )
                ->constrained(
                    'student_academic_years'
                )
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Jenis tagihan
            |--------------------------------------------------------------------------
            */

            $table->foreignId(
                'bill_type_id'
            )
                ->constrained(
                    'bill_types'
                )
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Periode tagihan
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | Januari 2026
            | Semester 1
            | Tahun ajaran tertentu
            |
            */

            $table->string(
                'period',
                50
            );


            /*
            |--------------------------------------------------------------------------
            | Nominal tagihan
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'amount',
                15,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | Jatuh tempo
            |--------------------------------------------------------------------------
            */

            $table->date(
                'due_date'
            )
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Status tagihan
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'status',
                [
                    'unpaid',
                    'partial',
                    'paid',
                    'cancelled',
                ]
            )
                ->default('unpaid');


            /*
            |--------------------------------------------------------------------------
            | Keterangan
            |--------------------------------------------------------------------------
            */

            $table->text(
                'description'
            )
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'student_academic_year_id',
                'status',
            ]);

            $table->index([
                'bill_type_id',
                'status',
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_bills'
        );
    }
};

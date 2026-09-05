<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_details', function (Blueprint $table) {

            $table->id();

            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('student_academic_year_id')
                ->constrained('student_academic_years')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('status', [
                'present',
                'sick',
                'permission',
                'absent',
            ]);

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'attendance_id',
                'student_academic_year_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};

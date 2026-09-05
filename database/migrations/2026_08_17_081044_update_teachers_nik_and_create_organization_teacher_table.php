<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Tambahkan NIK sementara
        |--------------------------------------------------------------------------
        |
        | Nullable terlebih dahulu karena mungkin ada data Guru lama
        | yang belum memiliki NIK.
        |
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('nik', 16)
                ->nullable()
                ->after('organization_id');

            $table->index('nik');
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Buat tabel relasi Guru - Organisasi
        |--------------------------------------------------------------------------
        */

        Schema::create('organization_teacher', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->primary([
                'organization_id',
                'teacher_id',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | 3. Pindahkan relasi lama ke tabel pivot
        |--------------------------------------------------------------------------
        |
        | Data organization_id yang saat ini ada di teachers
        | disalin ke organization_teacher.
        |
        */

        DB::statement('
            INSERT INTO organization_teacher (organization_id, teacher_id)
            SELECT organization_id, id
            FROM teachers
            WHERE organization_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_teacher');

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->dropColumn('nik');
        });
    }
};

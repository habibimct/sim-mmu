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
        /*
        |--------------------------------------------------------------------------
        | 1. Jadikan NIK wajib dan unik
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('nik', 16)
                ->nullable(false)
                ->unique()
                ->change();
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Hapus organization_id lama
        |--------------------------------------------------------------------------
        |
        | Relasi Guru dengan organisasi sekarang menggunakan tabel:
        |
        | organization_teacher
        |
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropIndex(['organization_id', 'is_active']);
            $table->dropColumn('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Kembalikan organization_id
        |--------------------------------------------------------------------------
        |
        | Tidak bisa menentukan organisasi secara otomatis untuk semua Guru
        | jika satu Guru sudah memiliki beberapa organisasi.
        |
        | Oleh karena itu kolom dikembalikan sebagai nullable.
        |
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['organization_id', 'is_active']);
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Kembalikan NIK menjadi nullable dan non-unique
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropUnique(['nik']);

            $table->string('nik', 16)
                ->nullable()
                ->change();
        });
    }
};

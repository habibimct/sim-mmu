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
        | Pindahkan nilai NIP lama ke kolom NIK
        |--------------------------------------------------------------------------
        */

        DB::statement('
            UPDATE teachers
            SET nik = nip
            WHERE nik IS NULL
              AND nip IS NOT NULL
        ');

        /*
        |--------------------------------------------------------------------------
        | Hapus kolom NIP lama
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Kembalikan kolom NIP
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('nip', 16)
                ->nullable()
                ->after('nik');
        });

        /*
        |--------------------------------------------------------------------------
        | Kembalikan data NIK ke NIP
        |--------------------------------------------------------------------------
        */

        DB::statement('
            UPDATE teachers
            SET nip = nik
            WHERE nip IS NULL
              AND nik IS NOT NULL
        ');
    }
};

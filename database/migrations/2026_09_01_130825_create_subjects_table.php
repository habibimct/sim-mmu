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
        Schema::create('subjects', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Organisasi / Unit
            |--------------------------------------------------------------------------
            */

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Identitas Mata Pelajaran
            |--------------------------------------------------------------------------
            */

            $table->string('code', 50);

            $table->string('name', 150);


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Satu kode mata pelajaran tidak boleh
            | duplikat dalam organisasi yang sama.
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'organization_id',
                'code',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

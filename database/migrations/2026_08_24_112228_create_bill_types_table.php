<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_types', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Organisasi / Unit pemilik jenis tagihan
            |--------------------------------------------------------------------------
            */

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identitas jenis tagihan
            |--------------------------------------------------------------------------
            */

            $table->string('code', 50);

            $table->string('name', 100);

            $table->text('description')
                ->nullable();

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
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'organization_id',
                'is_active',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Kode harus unik dalam satu organisasi
            |--------------------------------------------------------------------------
            |
            | MA boleh memiliki:
            |   SPP
            |
            | MTs juga boleh memiliki:
            |   SPP
            |
            | Tetapi dalam MA tidak boleh ada dua kode SPP.
            |
            */

            $table->unique([
                'organization_id',
                'code',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_types');
    }
};

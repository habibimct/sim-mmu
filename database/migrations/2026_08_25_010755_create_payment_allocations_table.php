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
        Schema::create('payment_allocations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Pembayaran
            |--------------------------------------------------------------------------
            */

            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Tagihan siswa
            |--------------------------------------------------------------------------
            */

            $table->foreignId('student_bill_id')
                ->constrained('student_bills')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Jumlah yang dialokasikan ke tagihan
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'amount',
                15,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | Keterangan
            |--------------------------------------------------------------------------
            */

            $table->text(
                'description'
            )->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Satu pembayaran tidak boleh memiliki dua
            | allocation untuk tagihan yang sama.
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'payment_id',
                'student_bill_id',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'student_bill_id'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'payment_allocations'
        );
    }
};

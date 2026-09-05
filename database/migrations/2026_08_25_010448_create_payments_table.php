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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Organisasi / unit penerima pembayaran
            |--------------------------------------------------------------------------
            */

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Nomor pembayaran internal PMUB
            |--------------------------------------------------------------------------
            */

            $table->string('payment_number', 50)
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | Tanggal pembayaran
            |--------------------------------------------------------------------------
            */

            $table->dateTime('payment_date');


            /*
            |--------------------------------------------------------------------------
            | Total pembayaran
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'amount',
                15,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | Metode pembayaran
            |--------------------------------------------------------------------------
            |
            | cash
            | bank_transfer
            | online
            |
            */

            $table->string(
                'payment_method',
                30
            );


            /*
            |--------------------------------------------------------------------------
            | Provider pembayaran
            |--------------------------------------------------------------------------
            |
            | null          = pembayaran manual
            | midtrans      = pembayaran online Midtrans
            |
            */

            $table->string(
                'payment_provider',
                30
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | Status pembayaran
            |--------------------------------------------------------------------------
            |
            | pending
            | confirmed
            | failed
            | cancelled
            |
            */

            $table->string(
                'status',
                30
            )->default('pending');


            /*
            |--------------------------------------------------------------------------
            | Referensi provider
            |--------------------------------------------------------------------------
            */

            $table->string(
                'provider_transaction_id',
                100
            )->nullable();

            $table->string(
                'provider_order_id',
                100
            )->nullable();

            $table->string(
                'provider_status',
                50
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | Keterangan
            |--------------------------------------------------------------------------
            */

            $table->text(
                'description'
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | User yang mencatat pembayaran
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | User yang mengonfirmasi pembayaran
            |--------------------------------------------------------------------------
            */

            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->dateTime(
                'confirmed_at'
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'organization_id',
                'payment_date',
            ]);

            $table->index(
                'status'
            );

            $table->index(
                'payment_method'
            );

            $table->index(
                'provider_transaction_id'
            );

            $table->index(
                'provider_order_id'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'payments'
        );
    }
};

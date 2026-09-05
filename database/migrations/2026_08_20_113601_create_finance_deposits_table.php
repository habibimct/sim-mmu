<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_deposits', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Organisasi pengirim
            |--------------------------------------------------------------------------
            */

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Organisasi penerima / parent
            |--------------------------------------------------------------------------
            */

            $table->foreignId('target_organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Data setoran
            |--------------------------------------------------------------------------
            */

            $table->date('deposit_date');

            $table->decimal(
                'amount',
                15,
                2
            );

            $table->enum(
                'payment_method',
                [
                    'cash',
                    'bank_transfer',
                    'online',
                ]
            );

            $table->string(
                'description'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Bukti setoran
            |--------------------------------------------------------------------------
            */

            $table->string(
                'proof_path'
            )->nullable();

            $table->string(
                'proof_original_name'
            )->nullable();

            $table->unsignedInteger(
                'proof_size'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'status',
                [
                    'pending',
                    'confirmed',
                    'rejected',
                ]
            )->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Dibuat oleh
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Konfirmasi
            |--------------------------------------------------------------------------
            */

            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp(
                'confirmed_at'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Penolakan
            |--------------------------------------------------------------------------
            */

            $table->text(
                'rejection_reason'
            )->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'organization_id',
                'status',
            ]);

            $table->index([
                'target_organization_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'finance_deposits'
        );
    }
};

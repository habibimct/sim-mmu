<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transfers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('from_organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            $table->foreignId('to_organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            $table->date('transfer_date');

            $table->decimal('amount', 15, 2);

            $table->string('payment_method', 50);

            $table->string('description')
                ->nullable();

            $table->enum('status', [
                'pending',
                'confirmed',
                'rejected',
            ])->default('pending');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('confirmed_at')
                ->nullable();

            $table->text('rejection_reason')
                ->nullable();

            $table->timestamps();

            $table->index([
                'from_organization_id',
                'to_organization_id',
            ]);

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transfers');
    }
};

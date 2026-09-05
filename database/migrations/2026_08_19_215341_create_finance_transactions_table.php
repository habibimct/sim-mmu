<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->restrictOnDelete();

            $table->date('transaction_date');

            $table->enum('type', [
                'income',
                'expense',
            ]);

            $table->decimal('amount', 15, 2);

            $table->string('payment_method', 50);

            $table->string('category', 100);

            $table->string('source_type', 50)
                ->default('normal');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('description')
                ->nullable();

            $table->enum('status', [
                'pending',
                'confirmed',
                'rejected',
            ])->default('confirmed');

            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('confirmed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'organization_id',
                'transaction_date',
            ]);

            $table->index([
                'type',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};

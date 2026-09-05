<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {

            $table->foreignId('finance_transfer_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('finance_transfers')
                ->nullOnDelete();

            $table->index('finance_transfer_id');
        });
    }

    public function down(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->dropForeign([
                'finance_transfer_id',
            ]);

            $table->dropIndex([
                'finance_transfer_id',
            ]);

            $table->dropColumn(
                'finance_transfer_id'
            );
        });
    }
};

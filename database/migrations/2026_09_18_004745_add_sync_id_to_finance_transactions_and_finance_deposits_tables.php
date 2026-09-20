<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->uuid('sync_id')
                ->nullable()
                ->unique()
                ->after('id');
        });

        Schema::table('finance_deposits', function (Blueprint $table) {
            $table->uuid('sync_id')
                ->nullable()
                ->unique()
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->dropUnique([
                'finance_transactions_sync_id_unique',
            ]);

            $table->dropColumn('sync_id');
        });

        Schema::table('finance_deposits', function (Blueprint $table) {
            $table->dropUnique([
                'finance_deposits_sync_id_unique',
            ]);

            $table->dropColumn('sync_id');
        });
    }
};

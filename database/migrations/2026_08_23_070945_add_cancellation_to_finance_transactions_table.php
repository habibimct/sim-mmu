<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Tambahkan status cancelled
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'finance_transactions',
            function (Blueprint $table) {

                $table->text('cancellation_reason')
                    ->nullable()
                    ->after('description');

                $table->foreignId('cancelled_by')
                    ->nullable()
                    ->after('cancellation_reason')
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('cancelled_at')
                    ->nullable()
                    ->after('cancelled_by');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Ubah enum status
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE finance_transactions
            MODIFY status ENUM(
                'pending',
                'confirmed',
                'rejected',
                'cancelled'
            )
            NOT NULL DEFAULT 'confirmed'
        ");
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan tidak ada status cancelled
        |--------------------------------------------------------------------------
        */

        DB::table('finance_transactions')
            ->where('status', 'cancelled')
            ->update([
                'status' => 'rejected',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Kembalikan enum status
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE finance_transactions
            MODIFY status ENUM(
                'pending',
                'confirmed',
                'rejected'
            )
            NOT NULL DEFAULT 'confirmed'
        ");

        /*
        |--------------------------------------------------------------------------
        | Hapus kolom pembatalan
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'finance_transactions',
            function (Blueprint $table) {

                $table->dropForeign([
                    'cancelled_by',
                ]);

                $table->dropColumn([
                    'cancellation_reason',
                    'cancelled_by',
                    'cancelled_at',
                ]);
            }
        );
    }
};

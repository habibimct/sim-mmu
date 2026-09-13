<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('logo_path')
                ->nullable()
                ->after('is_active');

            $table->text('address')
                ->nullable()
                ->after('logo_path');

            $table->string('phone', 50)
                ->nullable()
                ->after('address');

            $table->string('email')
                ->nullable()
                ->after('phone');

            $table->string('website')
                ->nullable()
                ->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'address',
                'phone',
                'email',
                'website',
            ]);
        });
    }
};

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
        Schema::create('offline_sync_requests', function (Blueprint $table) {
            $table->id();

            $table->uuid('sync_id')->unique();

            $table->string('type', 50);

            $table->string('status', 30)->default('acknowledged');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_sync_requests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transfer_attachments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('finance_transfer_id')
                ->constrained('finance_transfers')
                ->cascadeOnDelete();

            $table->string('file_path');

            $table->string('original_name')
                ->nullable();

            $table->string('mime_type', 100);

            $table->unsignedInteger('file_size');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index('finance_transfer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transfer_attachments');
    }
};

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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nip')->nullable();
            $table->string('name');
            $table->enum('gender', ['male', 'female']);

            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['organization_id', 'is_active']);
            $table->index('nip');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

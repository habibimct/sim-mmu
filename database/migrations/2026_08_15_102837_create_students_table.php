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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nis', 50);
            $table->string('name');
            $table->string('gender', 1);
            $table->date('birth_date')->nullable();
            $table->string('class_name', 100)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'organization_id',
                'nis',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->string('descripcion')->nullable();
            $table->boolean('plantilla')->nullable()->default(false);
            $table->boolean('completada')->nullable()->default(false);

            $table->foreignId('curso_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrics');
    }
};

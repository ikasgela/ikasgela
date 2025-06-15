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
        Schema::create('actividad_rubric', function (Blueprint $table) {
            $table->id();

            $table->ulid('orden')->index()->nullable();
            $table->boolean('titulo_visible')->default(true)->nullable();
            $table->boolean('descripcion_visible')->default(true)->nullable();
            $table->integer('columnas')->default(6)->nullable();

            $table->foreignId('rubric_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividad_rubric');
    }
};

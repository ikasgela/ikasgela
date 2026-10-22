<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Posición de cada plantilla en el grafo de encadenado (vista de nodos).
     */
    public function up(): void
    {
        Schema::create('actividad_grafos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('actividad_id')->unique()->constrained('actividades')->cascadeOnDelete();

            $table->double('posicion_x')->default(0);
            $table->double('posicion_y')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_grafos');
    }
};

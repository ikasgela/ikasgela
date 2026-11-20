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
        Schema::table('milestones', function (Blueprint $table) {
            $table->string('etiquetas')->default('base')->after('ajuste_proporcional_nota');
            $table->integer('num_actividades_obligatorias')->nullable()->after('etiquetas');

            // Nueva opción de ajuste: 'fijo'
            $table->enum('ajuste_proporcional_nota', ['media', 'mediana', 'fijo'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn(['etiquetas', 'num_actividades_obligatorias']);
            $table->enum('ajuste_proporcional_nota', ['media', 'mediana'])->nullable()->change();
        });
    }
};

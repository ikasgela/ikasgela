<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * R-EVAL-20: un milestone puede tener su propia cualificación (evaluación
     * parcial). Si la tiene, el cálculo de la nota la usa como cualificación
     * base en lugar de la del curso.
     */
    public function up(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->foreignId('qualification_id')
                ->nullable()
                ->after('num_actividades_obligatorias')
                ->constrained('qualifications')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('qualification_id');
        });
    }
};

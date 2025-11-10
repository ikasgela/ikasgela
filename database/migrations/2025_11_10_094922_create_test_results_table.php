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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->string('descripcion')->nullable();
            $table->boolean('plantilla')->nullable()->default(false);
            $table->boolean('completado')->nullable()->default(false);

            $table->integer('num_preguntas')->default(0);
            $table->double('valor_correcta')->default(1.0);
            $table->double('valor_incorrecta')->default(-0.25);
            $table->integer('num_correctas')->nullable()->default(0);
            $table->integer('num_incorrectas')->nullable()->default(0);

            $table->foreignId('curso_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadSelectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_selector', function (Blueprint $table) {
            $table->id();

            $table->foreignId('actividad_id')->constrained()->on('actividades')->onDelete('cascade');
            $table->foreignId('selector_id')->constrained()->onDelete('cascade');

            $table->uuid('orden')->index()->nullable();
            $table->boolean('titulo_visible')->default(true)->nullable();
            $table->boolean('descripcion_visible')->default(true)->nullable();
            $table->integer('columnas')->default(6)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad_selector');
    }
}

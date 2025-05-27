<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('unidad_id')->unsigned()->index();
            $table->foreign('unidad_id')->references('id')->on('unidades')->onDelete('cascade');

            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->integer('puntuacion')->nullable();

            $table->boolean('plantilla')->nullable()->default(false);

            $table->string('slug');

            $table->bigInteger('siguiente_id')->unsigned()->index()->nullable();
            $table->foreign('siguiente_id')->references('id')->on('actividades')->onDelete('set null');
            $table->boolean('final')->nullable()->default(false);

            $table->boolean('auto_avance')->nullable()->default(false);

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
        Schema::dropIfExists('actividades');
    }
}

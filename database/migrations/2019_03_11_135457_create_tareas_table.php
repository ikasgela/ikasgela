<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned()->index();;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('actividad_id')->unsigned()->index();;
            $table->foreign('actividad_id')->references('id')->on('actividades')->onDelete('cascade');

            $table->dateTimeTz('aceptada')->nullable();
            $table->dateTimeTz('completada')->nullable();
            $table->dateTimeTz('revisada')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('puntuacion')->nullable();

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
        Schema::dropIfExists('tareas');
    }
}

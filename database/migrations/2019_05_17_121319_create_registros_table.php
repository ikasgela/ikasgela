<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registros', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('tarea_id')->unsigned()->index();
            $table->foreign('tarea_id')->references('id')->on('tareas')->onDelete('cascade');

            $table->integer('estado_inicial')->nullable()->index()->default(10);
            $table->integer('estado_final')->nullable()->index()->default(10);
            $table->dateTimeTz('timestamp')->nullable();
            $table->text('detalles')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registros');
    }
}

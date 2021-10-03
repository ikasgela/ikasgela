<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJPlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('j_plags', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('tarea_id')->unsigned()->index();
            $table->foreign('tarea_id')->references('id')->on('tareas')->onDelete('cascade');

            $table->string('repository');
            $table->decimal('match', 8, 2);

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
        Schema::dropIfExists('j_plags');
    }
}

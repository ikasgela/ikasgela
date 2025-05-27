<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preguntas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('titulo');
            $table->string('texto');
            $table->boolean('multiple')->nullable()->default(false);
            $table->boolean('respondida')->nullable()->default(false);
            $table->boolean('correcta')->nullable()->default(false);
            $table->string('imagen')->nullable();

            $table->bigInteger('cuestionario_id')->unsigned()->index();
            $table->foreign('cuestionario_id')->references('id')->on('cuestionarios')->onDelete('cascade');

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
        Schema::dropIfExists('preguntas');
    }
}

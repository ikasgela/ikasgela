<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('texto');
            $table->boolean('correcto')->nullable()->default('false');
            $table->string('feedback_ok')->nullable();
            $table->string('feedback_error')->nullable();
            $table->integer('orden')->nullable();

            $table->bigInteger('pregunta_id')->unsigned()->index();
            $table->foreign('pregunta_id')->references('id')->on('preguntas')->onDelete('cascade');

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
        Schema::dropIfExists('items');
    }
}

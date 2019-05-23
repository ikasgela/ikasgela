<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkdownTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markdown_texts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('titulo');
            $table->string('descripcion')->nullable();
            $table->string('repositorio');
            $table->string('rama')->nullable();
            $table->string('archivo');

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
        Schema::dropIfExists('markdown_texts');
    }
}

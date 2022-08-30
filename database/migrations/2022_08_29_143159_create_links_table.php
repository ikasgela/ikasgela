<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();

            $table->string('url', 4096);
            $table->string('descripcion')->nullable();
            $table->integer('orden')->nullable();

            $table->bigInteger('link_collection_id')->unsigned()->index()->nullable();
            $table->foreign('link_collection_id')->references('id')->on('link_collections');

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
        Schema::dropIfExists('links');
    }
}

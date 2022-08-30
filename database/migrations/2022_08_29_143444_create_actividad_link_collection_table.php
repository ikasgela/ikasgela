<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadLinkCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_link_collection', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('link_collection_id')->unsigned()->index();
            $table->foreign('link_collection_id')->references('id')->on('link_collections')->onDelete('cascade');
            $table->bigInteger('actividad_id')->unsigned()->index();
            $table->foreign('actividad_id')->references('id')->on('actividades')->onDelete('cascade');

            $table->uuid('orden')->index()->nullable();

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
        Schema::dropIfExists('actividad_link_collection');
    }
}

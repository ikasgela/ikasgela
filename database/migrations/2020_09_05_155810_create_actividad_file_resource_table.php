<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadFileResourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_file_resource', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('file_resource_id')->unsigned()->index();
            $table->foreign('file_resource_id')->references('id')->on('file_resources')->onDelete('cascade');
            $table->bigInteger('actividad_id')->unsigned()->index();
            $table->foreign('actividad_id')->references('id')->on('actividades')->onDelete('cascade');

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
        Schema::dropIfExists('actividad_file_resource');
    }
}

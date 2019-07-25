<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadFileUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_file_upload', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('file_upload_id')->unsigned()->index();
            $table->foreign('file_upload_id')->references('id')->on('file_uploads')->onDelete('cascade');
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
        Schema::dropIfExists('actividad_file_upload');
    }
}

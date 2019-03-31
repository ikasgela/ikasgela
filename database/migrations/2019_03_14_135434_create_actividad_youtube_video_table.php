<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadYoutubeVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_youtube_video', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('youtube_video_id')->unsigned()->index();
            $table->foreign('youtube_video_id')->references('id')->on('youtube_videos')->onDelete('cascade');
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
        Schema::dropIfExists('actividad_youtube_video');
    }
}

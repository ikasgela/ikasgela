<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadIntellijProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_intellij_project', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('intellij_project_id')->unsigned()->index();
            $table->foreign('intellij_project_id')->references('id')->on('intellij_projects')->onDelete('cascade');
            $table->bigInteger('actividad_id')->unsigned()->index();
            $table->foreign('actividad_id')->references('id')->on('actividades')->onDelete('cascade');

            $table->string('fork')->nullable();

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
        Schema::dropIfExists('actividad_intellij_project');
    }
}

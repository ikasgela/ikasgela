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

            $table->bigInteger('intellij_project_id')->unsigned()->index();
            $table->foreign('intellij_project_id')->references('id')->on('intellij_projects')->onDelete('cascade');

            $table->bigInteger('match_id')->unsigned()->index();
            $table->foreign('match_id')->references('id')->on('actividades')->onDelete('cascade');

            $table->decimal('percent', 8, 2);

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

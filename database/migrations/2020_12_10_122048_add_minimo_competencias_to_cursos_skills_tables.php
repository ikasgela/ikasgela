<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimoCompetenciasToCursosSkillsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->integer('minimo_competencias')->nullable();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->integer('minimo_competencias')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('minimo_competencias');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('minimo_competencias');
        });
    }
}

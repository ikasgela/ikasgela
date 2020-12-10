<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimoExamenesToCursosUnidadesSkillsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->integer('minimo_examenes')->nullable();
            $table->boolean('examenes_obligatorios')->default(false);
        });

        Schema::table('unidades', function (Blueprint $table) {
            $table->integer('minimo_examenes')->nullable();
            $table->boolean('examenes_obligatorios')->default(false);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->integer('minimo_examenes')->nullable();
            $table->boolean('examenes_obligatorios')->default(false);
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
            $table->dropColumn('minimo_examenes');
            $table->dropColumn('examenes_obligatorios');
        });

        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn('minimo_examenes');
            $table->dropColumn('examenes_obligatorios');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('minimo_examenes');
            $table->dropColumn('examenes_obligatorios');
        });
    }
}

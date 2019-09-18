<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxSimultaneasToCursoUsuarioTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->bigInteger('max_simultaneas')->unsigned()->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('max_simultaneas')->unsigned()->nullable();
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
            $table->dropColumn('max_simultaneas');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('max_simultaneas');
        });
    }
}

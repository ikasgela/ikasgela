<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsFromTareas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('aceptada');
            $table->dropColumn('enviada');
            $table->dropColumn('revisada');
            $table->dropColumn('terminada');
            $table->dropColumn('archivada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dateTimeTz('aceptada')->nullable();
            $table->dateTimeTz('enviada')->nullable();
            $table->dateTimeTz('revisada')->nullable();
            $table->dateTimeTz('terminada')->nullable();
            $table->dateTimeTz('archivada')->nullable();
        });
    }
}

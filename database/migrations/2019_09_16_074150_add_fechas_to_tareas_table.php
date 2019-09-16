<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechasToTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('fecha_limite');
        });

        Schema::table('actividades', function (Blueprint $table) {
            $table->dateTimeTz('fecha_disponibilidad')->nullable();
            $table->dateTimeTz('fecha_entrega')->nullable();
            $table->dateTimeTz('fecha_limite')->nullable();
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
            $table->dateTimeTz('fecha_limite')->nullable();
        });

        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn('fecha_disponibilidad');
            $table->dropColumn('fecha_entrega');
            $table->dropColumn('fecha_limite');
        });
    }
}

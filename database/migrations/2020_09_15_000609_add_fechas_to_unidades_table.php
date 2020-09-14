<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechasToUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidades', function (Blueprint $table) {
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
        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn('fecha_disponibilidad');
            $table->dropColumn('fecha_entrega');
            $table->dropColumn('fecha_limite');
        });
    }
}

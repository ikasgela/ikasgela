<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimoEntregadasToCursosUnidadesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->integer('minimo_entregadas')->nullable();
        });

        Schema::table('unidades', function (Blueprint $table) {
            $table->integer('minimo_entregadas')->nullable();
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
            $table->dropColumn('minimo_entregadas');
        });

        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn('minimo_entregadas');
        });
    }
}

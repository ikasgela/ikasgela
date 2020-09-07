<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlantillaIdToActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->bigInteger('plantilla_id')->unsigned()->index()->nullable();
            $table->foreign('plantilla_id')->references('id')->on('actividades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign('actividades_plantilla_id_foreign');
            $table->dropIndex('actividades_plantilla_id_index');
            $table->dropColumn('plantilla_id');
        });
    }
}

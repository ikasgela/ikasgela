<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsForkingToActividadIntellijProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividad_intellij_project', function (Blueprint $table) {
            $table->boolean('is_forking')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividad_intellij_project', function (Blueprint $table) {
            $table->dropColumn('is_forking');
        });
    }
}

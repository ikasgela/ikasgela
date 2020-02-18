<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForkStatusInActividadIntellijprojectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividad_intellij_project', function (Blueprint $table) {
            $table->integer('fork_status')->nullable()->default(0)->change();
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
            $table->boolean('fork_status')->nullable()->default(false)->change();
        });
    }
}

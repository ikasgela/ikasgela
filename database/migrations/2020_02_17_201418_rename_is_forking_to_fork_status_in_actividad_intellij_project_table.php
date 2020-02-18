<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIsForkingToForkStatusInActividadIntellijProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividad_intellij_project', function (Blueprint $table) {
            $table->renameColumn('is_forking', 'fork_status');
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
            $table->renameColumn('fork_status', 'is_forking');
        });
    }
}

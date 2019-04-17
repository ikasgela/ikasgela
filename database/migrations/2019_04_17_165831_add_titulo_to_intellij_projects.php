<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTituloToIntellijProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('intellij_projects', function (Blueprint $table) {
            $table->string('titulo');
            $table->string('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intellij_projects', function (Blueprint $table) {
            $table->dropColumn('titulo');
            $table->dropColumn('descripcion');
        });
    }
}

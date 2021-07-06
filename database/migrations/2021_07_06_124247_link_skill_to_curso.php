<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkSkillToCurso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skills', function (Blueprint $table) {

            // Borrar la relación s -> org
            $table->dropForeign('skills_organization_id_foreign');
            $table->dropIndex('skills_organization_id_index');
            $table->bigInteger('organization_id')->unsigned()->nullable()->change();

            // Renombrar el campo org a curso
            $table->renameColumn('organization_id', 'curso_id');
        });

        Schema::table('skills', function (Blueprint $table) {

            // Crear la relación s -> curso
            $table->bigInteger('curso_id')->unsigned()->index()->nullable(false)->change();
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skills', function (Blueprint $table) {

            // Borrar la relación s -> curso
            $table->dropForeign('skills_curso_id_foreign');
            $table->dropIndex('skills_curso_id_index');

            // Renombrar el campo curso a org
            $table->renameColumn('curso_id', 'organization_id');
        });

        Schema::table('skills', function (Blueprint $table) {

            // Crear la relación s -> org
            $table->bigInteger('organization_id')->unsigned()->index()->nullable(false)->change();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }
}

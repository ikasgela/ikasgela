<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkQualificationToCurso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qualifications', function (Blueprint $table) {

            // Borrar la relación q -> org
            $table->dropForeign('qualifications_organization_id_foreign');
            $table->dropIndex('qualifications_organization_id_index');
            $table->bigInteger('organization_id')->unsigned()->nullable()->change();

            // Renombrar el campo org a curso
            $table->renameColumn('organization_id', 'curso_id');
        });

        Schema::table('qualifications', function (Blueprint $table) {

            // Crear la relación q -> curso
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
        Schema::table('qualifications', function (Blueprint $table) {

            // Borrar la relación q -> curso
            $table->dropForeign('qualifications_curso_id_foreign');
            $table->dropIndex('qualifications_curso_id_index');

            // Renombrar el campo curso a org
            $table->renameColumn('curso_id', 'organization_id');
        });

        Schema::table('qualifications', function (Blueprint $table) {

            // Crear la relación q -> org
            $table->bigInteger('organization_id')->unsigned()->index()->nullable(false)->change();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }
}

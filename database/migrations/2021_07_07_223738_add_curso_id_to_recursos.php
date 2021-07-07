<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCursoIdToRecursos extends Migration
{
    private $recursos = [
        'intellij_projects',
        'youtube_videos',
        'markdown_texts',
        'cuestionarios',
        'file_uploads',
        'file_resources',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->recursos as $recurso) {
            Schema::table($recurso, function (Blueprint $table) {
                $table->bigInteger('curso_id')->unsigned()->index()->nullable();
                $table->foreign('curso_id')->references('id')->on('cursos');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->recursos as $recurso) {
            Schema::table($recurso, function (Blueprint $table) use ($recurso) {
                $table->dropForeign($recurso . '_curso_id_foreign');
                $table->dropIndex($recurso . '_curso_id_index');
                $table->dropColumn('curso_id');
            });
        }
    }
}

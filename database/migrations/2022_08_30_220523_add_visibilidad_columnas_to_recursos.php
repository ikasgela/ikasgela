<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilidadColumnasToRecursos extends Migration
{
    private $recursos = [
        'intellij_projects',
        'youtube_videos',
        'markdown_texts',
        'cuestionarios',
        'file_uploads',
        'file_resources',
        'link_collections',
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
                $table->boolean('titulo_visible')->default(true)->nullable();
                $table->boolean('descripcion_visible')->default(true)->nullable();
                $table->integer('columnas')->default(6)->nullable();
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
                $table->dropColumn('titulo_visible');
                $table->dropColumn('descripcion_visible');
                $table->dropColumn('columnas');
            });
        }
    }
}

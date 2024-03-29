<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToRecursos extends Migration
{
    private $recursos = [
        'intellij_project',
        'youtube_video',
        'markdown_text',
        'cuestionario',
        'file_upload',
        'file_resource',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->recursos as $recurso) {
            Schema::table("actividad_$recurso", function (Blueprint $table) {
                $table->uuid('orden')->index()->nullable();
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
            Schema::table("actividad_$recurso", function (Blueprint $table) use ($recurso) {
                $table->dropIndex("actividad_{$recurso}_orden_index");
                $table->dropColumn('orden');
            });
        }
    }
}

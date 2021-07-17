<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToRecursos extends Migration
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
            Schema::table($recurso, function (Blueprint $table) use ($recurso) {
                $table->dropIndex($recurso . '_orden_index');
                $table->dropColumn('orden');
            });
        }
    }
}

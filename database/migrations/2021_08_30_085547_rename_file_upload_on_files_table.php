<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFileUploadOnFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->renameColumn('file_upload_id', 'uploadable_id');
            $table->renameColumn('file_upload_type', 'uploadable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->renameColumn('uploadable_id', 'file_upload_id');
            $table->renameColumn('uploadable_type', 'file_upload_type');
        });
    }
}

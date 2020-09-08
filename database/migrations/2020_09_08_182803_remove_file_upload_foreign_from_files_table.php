<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFileUploadForeignFromFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign('files_file_upload_id_foreign');
            $table->dropIndex('files_file_upload_id_index');
            $table->bigInteger('file_upload_id')->unsigned()->index()->nullable()->change();
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
            $table->dropIndex('files_file_upload_id_index');
            $table->bigInteger('file_upload_id')->unsigned()->index()->change();
            $table->foreign('file_upload_id')->references('id')->on('file_uploads')->onDelete('cascade');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCategoryIdNotNullInCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->bigInteger('category_id')->unsigned()->index()->nullable(false)->change();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeign('cursos_category_id_foreign');
            $table->dropIndex('cursos_category_id_index');
            $table->bigInteger('category_id')->unsigned()->nullable()->change();
        });
    }
}

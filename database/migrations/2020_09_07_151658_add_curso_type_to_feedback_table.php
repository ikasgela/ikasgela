<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCursoTypeToFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->string('curso_type')->default('App\\\Curso');

            $table->dropForeign('feedback_curso_id_foreign');
            $table->dropIndex('feedback_curso_id_index');
            $table->bigInteger('curso_id')->unsigned()->index()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn('curso_type');

            $table->dropIndex('feedback_curso_id_index');
            $table->bigInteger('curso_id')->unsigned()->index()->change();
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });
    }
}

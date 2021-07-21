<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCursoTypeOnFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->renameColumn('curso_id', 'comentable_id');
            $table->renameColumn('curso_type', 'comentable_type');
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
            $table->renameColumn('comentable_id', 'curso_id');
            $table->renameColumn('comentable_type', 'curso_type');
        });
    }
}

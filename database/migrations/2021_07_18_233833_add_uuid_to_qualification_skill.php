<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToQualificationSkill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qualification_skill', function (Blueprint $table) {
            $table->uuid('orden')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualification_skill', function (Blueprint $table) {
            $table->dropIndex("qualification_skill_orden_index");
            $table->dropColumn('orden');
        });
    }
}

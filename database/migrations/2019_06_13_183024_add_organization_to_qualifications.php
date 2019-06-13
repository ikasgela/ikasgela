<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrganizationToQualifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qualifications', function (Blueprint $table) {
            $table->bigInteger('organization_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualifications', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::create('safe_exams', function (Blueprint $table) {
            $table->id();

            $table->string('token')->nullable();
            $table->string('quit_password')->nullable();

            $table->bigInteger('curso_id')->unsigned()->index()->nullable();
            $table->foreign('curso_id')->references('id')->on('cursos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safe_exams');

        Schema::table('cursos', function (Blueprint $table) {
            $table->string('token')->nullable();
        });
    }
};

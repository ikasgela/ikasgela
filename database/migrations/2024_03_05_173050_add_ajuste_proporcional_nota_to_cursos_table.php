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
            $table->enum('ajuste_proporcional_nota', ['media', 'mediana'])->nullable();
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->enum('ajuste_proporcional_nota', ['media', 'mediana'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('ajuste_proporcional_nota');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('ajuste_proporcional_nota');
        });
    }
};

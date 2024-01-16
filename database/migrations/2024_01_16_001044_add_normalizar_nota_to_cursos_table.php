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
            $table->boolean('normalizar_nota')->nullable()->default(false);
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->boolean('normalizar_nota')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('normalizar_nota');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('normalizar_nota');
        });
    }
};

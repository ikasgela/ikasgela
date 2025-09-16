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
        Schema::table('actividad_intellij_project', function (Blueprint $table) {
            $table->boolean('incluir_siempre')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividad_intellij_project', function (Blueprint $table) {
            $table->dropColumn('incluir_siempre');
        });
    }
};

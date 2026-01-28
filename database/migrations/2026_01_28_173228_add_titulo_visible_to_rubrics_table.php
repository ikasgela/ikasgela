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
        Schema::table('rubrics', function (Blueprint $table) {
            $table->boolean('titulo_visible')->default(true)->nullable();
            $table->boolean('descripcion_visible')->default(true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rubrics', function (Blueprint $table) {
            $table->dropColumn('titulo_visible');
            $table->dropColumn('descripcion_visible');
        });
    }
};

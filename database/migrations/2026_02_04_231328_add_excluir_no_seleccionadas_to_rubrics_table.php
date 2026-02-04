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
            $table->boolean('excluir_no_seleccionadas')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rubrics', function (Blueprint $table) {
            $table->dropColumn('excluir_no_seleccionadas');
        });
    }
};

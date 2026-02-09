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
        Schema::table('safe_exams', function (Blueprint $table) {
            $table->boolean('full_screen')->default(false)->nullable();
            $table->boolean('show_toolbar')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safe_exams', function (Blueprint $table) {
            $table->dropColumn('full_screen');
            $table->dropColumn('show_toolbar');
        });
    }
};

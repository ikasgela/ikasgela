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
        Schema::table('allowed_apps', function (Blueprint $table) {
            $table->boolean('disabled')->nullable()->default(false);
        });

        Schema::table('allowed_urls', function (Blueprint $table) {
            $table->boolean('disabled')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowed_apps', function (Blueprint $table) {
            $table->dropColumn('disabled');
        });

        Schema::table('allowed_urls', function (Blueprint $table) {
            $table->dropColumn('disabled');
        });
    }
};

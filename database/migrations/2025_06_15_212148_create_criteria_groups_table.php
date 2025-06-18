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
        Schema::create('criteria_groups', function (Blueprint $table) {
            $table->id();

            $table->string('titulo')->nullable();
            $table->string('descripcion')->nullable();
            $table->uuid('orden');

            $table->foreignId('rubric_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_groups');
    }
};

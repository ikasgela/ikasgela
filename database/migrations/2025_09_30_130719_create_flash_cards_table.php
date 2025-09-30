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
        Schema::create('flash_cards', function (Blueprint $table) {
            $table->id();

            $table->string('titulo')->nullable();
            $table->string('descripcion')->nullable();
            $table->text('anverso');
            $table->boolean('anverso_visible')->default(false)->nullable();
            $table->text('reverso')->nullable();
            $table->boolean('reverso_visible')->default(false)->nullable();
            $table->uuid('orden');

            $table->foreignId('flash_deck_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_cards');
    }
};

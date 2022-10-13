<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuleGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rule_groups', function (Blueprint $table) {
            $table->id();

            $table->string('operador');     // and, or
            $table->string('accion');       // siguiente
            $table->string('resultado');    // 10 (id_actividad)

            $table->foreignId('selector_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rule_groups');
    }
}

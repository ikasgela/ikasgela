<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Esquema de v5 de spatie/laravel-activitylog (ver UPGRADING.md del paquete):
 * - añade `attribute_changes` (cambios de atributos; en v4 vivían dentro de `properties`)
 * - elimina `batch_uuid` (sistema de lotes retirado en v5)
 * - mueve los cambios existentes de `properties` a `attribute_changes`
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->json('attribute_changes')->nullable()->after('causer_id');
            $table->dropColumn('batch_uuid');
        });

        // Mover los cambios de atributos registrados por v4 a la columna nueva
        DB::table('activity_log')
            ->where(function ($query) {
                $query->whereNotNull('properties->attributes')
                    ->orWhereNotNull('properties->old');
            })
            ->eachById(function ($row) {
                $properties = json_decode($row->properties, true) ?? [];
                $changes = array_intersect_key($properties, array_flip(['attributes', 'old']));
                $remaining = array_diff_key($properties, array_flip(['attributes', 'old']));

                DB::table('activity_log')->where('id', $row->id)->update([
                    'attribute_changes' => empty($changes) ? null : json_encode($changes),
                    'properties' => empty($remaining) ? null : json_encode($remaining),
                ]);
            });
    }

    public function down(): void
    {
        // Volver a v4: fusionar `attribute_changes` en `properties`
        DB::table('activity_log')
            ->whereNotNull('attribute_changes')
            ->eachById(function ($row) {
                $properties = json_decode($row->properties, true) ?? [];
                $properties = array_merge($properties, json_decode($row->attribute_changes, true) ?? []);

                DB::table('activity_log')->where('id', $row->id)->update([
                    'properties' => empty($properties) ? null : json_encode($properties),
                    'attribute_changes' => null,
                ]);
            });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
            $table->dropColumn('attribute_changes');
        });
    }
};

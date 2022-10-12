<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\MarkdownText;
use App\Models\Rule;
use App\Models\RuleGroup;
use App\Models\Selector;
use App\Models\Unidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SelectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $selector = Selector::factory()->create([
            'titulo' => 'Agenda: Repaso',
            'descripcion' => 'Asignar una actividad de refuerzo automáticamente.',
            'curso_id' => 1,
        ]);

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion-estructurada')
            ->first();

        $nombre = 'Agenda: Repaso';
        $actividad = Actividad::factory()->create(
            [
                'nombre' => $nombre,
                'descripcion' => 'Actividad de repaso.',
                'puntuacion' => 50,
                'slug' => Str::slug($nombre),
                'plantilla' => true,
                'tags' => 'repaso',
                'unidad_id' => $unidad->id,
            ]
        );

        $markdown = MarkdownText::where('titulo', 'Apuntes')->first();
        $actividad->markdown_texts()->attach($markdown, ['orden' => Str::orderedUuid()]);

        $rule_group = RuleGroup::factory()->create([
            'operador' => 'and',
            'accion' => 'siguiente',
            'resultado' => $actividad->id,
            'selector_id' => $selector->id,
        ]);

        Rule::factory()->create([
            'propiedad' => 'nota',
            'operador' => '<',
            'valor' => 80,
            'rule_group_id' => $rule_group->id,
        ]);

        Rule::factory()->create([
            'propiedad' => 'intentos',
            'operador' => '>=',
            'valor' => 2,
            'rule_group_id' => $rule_group->id,
        ]);

        $actividad = Actividad::where('slug', 'agenda')->first();
        $actividad->selectors()->attach($selector, ['orden' => Str::orderedUuid()]);
    }
}

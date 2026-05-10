<?php

namespace Tests\Unit;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Rule;
use App\Models\RuleGroup;
use App\Models\Selector;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use App\Models\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SelectorActividadTest extends TestCase
{
    use DatabaseTransactions;

    // ===== Selector::calcularResultado =====
    public function testSelectorCalcularResultadoWithAndGroup()
    {
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $selector = Selector::factory()->create();
        $ruleGroup = RuleGroup::factory()->create([
            'selector_id' => $selector->id,
            'operador' => 'AND',
            'resultado' => $actividad->id,
        ]);
        Rule::factory()->create([
            'rule_group_id' => $ruleGroup->id,
            'propiedad' => 'puntuacion',
            'operador' => '>=',
            'valor' => 5,
        ]);

        $tarea = Tarea::factory()->create([
            'actividad_id' => $actividad->id,
            'puntuacion' => 10,
            'estado' => 10,
        ]);

        $resultado = $selector->calcularResultado($actividad, $tarea);
        $this->assertEquals($actividad->id, $resultado);
    }

    public function testSelectorCalcularResultadoWithOrGroup()
    {
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $selector = Selector::factory()->create();
        $ruleGroup = RuleGroup::factory()->create([
            'selector_id' => $selector->id,
            'operador' => 'OR',
            'resultado' => $actividad->id,
        ]);
        Rule::factory()->create([
            'rule_group_id' => $ruleGroup->id,
            'propiedad' => 'intentos',
            'operador' => '>',
            'valor' => 0,
        ]);

        $tarea = Tarea::factory()->create([
            'actividad_id' => $actividad->id,
            'intentos' => 2,
            'estado' => 10,
        ]);

        $resultado = $selector->calcularResultado($actividad, $tarea);
        $this->assertEquals($actividad->id, $resultado);
    }

    public function testSelectorCalcularResultadoAllOperators()
    {
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $tarea = Tarea::factory()->create([
            'actividad_id' => $actividad->id,
            'puntuacion' => 7,
            'intentos' => 3,
            'estado' => 10,
        ]);

        // Test < operator
        $s1 = Selector::factory()->create();
        $rg1 = RuleGroup::factory()->create(['selector_id' => $s1->id, 'operador' => 'AND', 'resultado' => $actividad->id]);
        Rule::factory()->create(['rule_group_id' => $rg1->id, 'propiedad' => 'puntuacion', 'operador' => '<', 'valor' => 10]);
        $this->assertEquals($actividad->id, $s1->calcularResultado($actividad, $tarea));

        // Test <= operator
        $s2 = Selector::factory()->create();
        $rg2 = RuleGroup::factory()->create(['selector_id' => $s2->id, 'operador' => 'AND', 'resultado' => $actividad->id]);
        Rule::factory()->create(['rule_group_id' => $rg2->id, 'propiedad' => 'puntuacion', 'operador' => '<=', 'valor' => 7]);
        $this->assertEquals($actividad->id, $s2->calcularResultado($actividad, $tarea));

        // Test == operator
        $s3 = Selector::factory()->create();
        $rg3 = RuleGroup::factory()->create(['selector_id' => $s3->id, 'operador' => 'AND', 'resultado' => $actividad->id]);
        Rule::factory()->create(['rule_group_id' => $rg3->id, 'propiedad' => 'intentos', 'operador' => '==', 'valor' => 3]);
        $this->assertEquals($actividad->id, $s3->calcularResultado($actividad, $tarea));

        // Test != operator
        $s4 = Selector::factory()->create();
        $rg4 = RuleGroup::factory()->create(['selector_id' => $s4->id, 'operador' => 'AND', 'resultado' => $actividad->id]);
        Rule::factory()->create(['rule_group_id' => $rg4->id, 'propiedad' => 'intentos', 'operador' => '!=', 'valor' => 99]);
        $this->assertEquals($actividad->id, $s4->calcularResultado($actividad, $tarea));
    }

    public function testSelectorCalcularResultadoNoGroups()
    {
        $selector = Selector::factory()->create();
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $tarea = Tarea::factory()->create(['actividad_id' => $actividad->id, 'estado' => 10]);
        $resultado = $selector->calcularResultado($actividad, $tarea);
        $this->assertNull($resultado);
    }

    // ===== Actividad::duplicar_recursos =====
    public function testActividadDuplicarRecursos()
    {
        $curso = Curso::factory()->create();
        $actividad = Actividad::factory()->create(['plantilla' => true]);

        // Add a youtube video to the actividad
        $video = YoutubeVideo::factory()->create(['curso_id' => $curso->id]);
        $actividad->youtube_videos()->attach($video, [
            'orden' => 1,
            'titulo_visible' => false,
            'descripcion_visible' => false,
            'columnas' => 12,
        ]);

        // duplicar_recursos should copy the video and reconectar it
        $actividad->duplicar_recursos($curso);

        // After duplicar_recursos, the actividad should still have youtube videos
        $actividad->refresh();
        $this->assertCount(1, $actividad->youtube_videos);
    }

    public function testActividadDuplicarRecursosNull()
    {
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        // With no resources and null curso_destino - just runs empty loops
        $actividad->duplicar_recursos(null);
        $this->assertTrue(true);
    }

    // ===== Actividad::trasladar_recursos =====
    public function testActividadTrasladarRecursos()
    {
        $curso1 = Curso::factory()->create();
        $curso2 = Curso::factory()->create();
        $unidad = Unidad::factory()->create(['curso_id' => $curso1->id]);
        $actividad = Actividad::factory()->create(['unidad_id' => $unidad->id, 'plantilla' => true]);

        $video = YoutubeVideo::factory()->create(['curso_id' => $curso1->id]);
        $actividad->youtube_videos()->attach($video, [
            'orden' => 1,
            'titulo_visible' => false,
            'descripcion_visible' => false,
            'columnas' => 12,
        ]);

        $actividad->trasladar_recursos($curso2);

        $video->refresh();
        $this->assertEquals($curso2->id, $video->curso_id);
    }
}

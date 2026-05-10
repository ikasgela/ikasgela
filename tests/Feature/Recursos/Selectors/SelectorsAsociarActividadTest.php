<?php

namespace Tests\Feature\Recursos\Selectors;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Selector;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class SelectorsAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testActividad()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $selector1 = Selector::factory()->create(['curso_id' => $curso->id]);
        $selector2 = Selector::factory()->create(['curso_id' => $curso->id]);
        $selector3 = Selector::factory()->create(['curso_id' => $curso->id]);

        $actividad->selectors()->attach($selector1);
        $actividad->selectors()->attach($selector3);

        // When
        $response = $this->get(route('selectors.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSee($selector1->titulo);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $selector1 = Selector::factory()->create(['curso_id' => $curso->id]);
        $selector2 = Selector::factory()->create(['curso_id' => $curso->id]);

        // When
        $this->post(route('selectors.asociar', $actividad), ['seleccionadas' => [$selector1->id, $selector2->id]]);

        // Then
        $this->assertCount(2, $actividad->selectors()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('selectors.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $selector1 = Selector::factory()->create();
        $selector2 = Selector::factory()->create();

        $actividad->selectors()->attach($selector1);
        $actividad->selectors()->attach($selector2);

        // When
        $this->delete(route('selectors.desasociar', [$actividad, $selector1]));

        // Then
        $this->assertCount(1, $actividad->selectors()->get());
    }
}

<?php

namespace Tests\Feature\Recursos\LinkCollections;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\LinkCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LinkCollectionsAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

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
        $link_collection1 = LinkCollection::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $link_collection2 = LinkCollection::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $link_collection3 = LinkCollection::factory()->create([
            'curso_id' => $curso->id,
        ]);

        $actividad->link_collections()->attach($link_collection1);
        $actividad->link_collections()->attach($link_collection3);

        // When
        $response = $this->get(route('link_collections.actividad', $actividad));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Resources: Link collections'),
            __('Assigned resources'),
            $link_collection1->repositorio,
            $link_collection3->repositorio,
            __('Available resources'),
            $link_collection2->repositorio,
        ]);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $link_collection1 = LinkCollection::factory()->create([
            'curso_id' => $curso->id,
        ]);
        $link_collection2 = LinkCollection::factory()->create([
            'curso_id' => $curso->id,
        ]);

        // When
        $this->post(route('link_collections.asociar', $actividad), ['seleccionadas' => [$link_collection1, $link_collection2]]);

        // Then
        $this->assertCount(2, $actividad->link_collections()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('link_collections.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $link_collection1 = LinkCollection::factory()->create();
        $link_collection2 = LinkCollection::factory()->create();

        $actividad->link_collections()->attach($link_collection1);
        $actividad->link_collections()->attach($link_collection2);

        // When
        $this->delete(route('link_collections.desasociar', [$actividad, $link_collection1]));

        // Then
        $this->assertCount(1, $actividad->link_collections()->get());
    }
}

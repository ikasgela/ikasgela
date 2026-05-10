<?php

namespace Tests\Feature\Alumno;

use App\Models\Organization;
use App\Models\Period;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class AlumnoControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testPortada()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $organization = Organization::factory()->create(['slug' => 'ikasgela']);
        Period::factory()->create(['organization_id' => $organization->id]);

        // When
        $response = $this->get(route('users.portada'));

        // Then
        $response->assertSuccessful();
    }

    public function testPortadaFiltro()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $organization = Organization::factory()->create(['slug' => 'ikasgela']);
        Period::factory()->create(['organization_id' => $organization->id]);

        // When
        $response = $this->post(route('users.portada.filtro'), [
            'filtro_cursos_no_disponibles' => 'S',
        ]);

        // Then
        $response->assertSuccessful();
    }

    public function testNotAuthNotPortada()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('users.portada'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testTareas()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        // When
        $response = $this->get(route('users.home'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAlumnoNotTareas()
    {
        // Auth
        $this->actingAs($this->not_alumno_profesor);

        // Given
        // When
        $response = $this->get(route('users.home'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotTareas()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('users.home'));

        // Then
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Alumno;

use App\Models\Organization;
use App\Models\Period;
use App\Models\Curso;
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

    public function testPortadaUnauthenticated()
    {
        // When
        $response = $this->get(route('users.portada'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testPortadaAsProfesor()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $organization = Organization::factory()->create(['slug' => 'ikasgela']);
        Period::factory()->create(['organization_id' => $organization->id]);

        // When - portada doesn't have authorization, allows any auth user
        $response = $this->get(route('users.portada'));

        // Then
        $response->assertSuccessful();
    }

    public function testPortadaFiltroUnauthenticated()
    {
        // When
        $response = $this->post(route('users.portada.filtro'), [
            'filtro_cursos_no_disponibles' => 'S',
        ]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testPortadaFiltroAsProfesor()
    {
        $this->actingAs($this->profesor);

        $organization = Organization::factory()->create(['slug' => 'ikasgela']);
        Period::factory()->create(['organization_id' => $organization->id]);

        $response = $this->post(route('users.portada.filtro'), [
            'filtro_cursos_no_disponibles' => 'S',
        ]);

        // portada.filtro doesn't have authorization, allows any auth user
        $response->assertSuccessful();
    }

    public function testTareasAsProfesor()
    {
        $this->actingAs($this->profesor);

        $response = $this->get(route('users.home'));

        $response->assertForbidden();
    }

    public function testTareasAsTutor()
    {
        $this->actingAs($this->tutor);

        $response = $this->get(route('users.home'));

        $response->assertForbidden();
    }

    public function testTareasConCursoActual()
    {
        $this->actingAs($this->alumno);

        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        $response = $this->get(route('users.home'));

        $response->assertSuccessful();
    }

    public function testPortadaFiltroToggleOff()
    {
        $this->actingAs($this->alumno);

        $organization = Organization::factory()->create(['slug' => 'ikasgela']);
        Period::factory()->create(['organization_id' => $organization->id]);

        // Filtro ya activado en sesión
        session(['users_filtro_cursos_no_disponibles' => 'S']);

        // Al volver a enviar, hace toggle-off
        $response = $this->post(route('users.portada.filtro'), [
            'filtro_cursos_no_disponibles' => 'S',
        ]);

        $response->assertSuccessful();
    }
}

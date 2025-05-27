<?php

namespace Tests\Feature;

use App\Models\Registro;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Override;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tests\TestCase;

class RegistrosCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'user_id', 'tarea_id', 'estado'
    ];

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();

        App::setLocale('es');
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $registro = Registro::factory()->create();

        // When
        $response = $this->get(route('registros.index'));

        // Then
        $response->assertSuccessful()->assertSee(Carbon::parse($registro->timestamp)->format('d/m/Y H:i:s'));
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('registros.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('registros.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testNotCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException(RouteNotFoundException::class);

        // When
        $this->get(route('registros.create'));

        // Then
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $registro = Registro::factory()->make();
        $total = Registro::all()->count();

        // When
        $this->post(route('registros.store'), $registro->toArray());

        // Then
        $this->assertEquals($total + 1, Registro::all()->count());
    }

    public function testNotAlumnoProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_alumno_profesor);

        // Given
        $registro = Registro::factory()->make();

        // When
        $response = $this->post(route('registros.store'), $registro->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $registro = Registro::factory()->make();

        // When
        $response = $this->post(route('registros.store'), $registro->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $total = Registro::all()->count();

        $empty = new Registro();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('registros.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $registro = Registro::factory()->make([$field => null]);

        // When
        $response = $this->post(route('registros.store'), $registro->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testNotShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException(RouteNotFoundException::class);

        // When
        $this->get(route('registros.show'));

        // Then
    }

    public function testNotEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException(RouteNotFoundException::class);

        // When
        $this->get(route('registros.edit'));

        // Then
    }

    public function testNotUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException(RouteNotFoundException::class);

        // When
        $this->put(route('registros.update'));

        // Then
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $registro = Registro::factory()->create();

        // When
        $response = $this->delete(route('registros.destroy', $registro));

        // Then
        $response->assertNotFound();
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $registro = Registro::factory()->create();

        // When
        $response = $this->delete(route('registros.destroy', $registro));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $registro = Registro::factory()->create();

        // When
        $response = $this->delete(route('registros.destroy', $registro));

        // Then
        $response->assertRedirect(route('login'));
    }
}

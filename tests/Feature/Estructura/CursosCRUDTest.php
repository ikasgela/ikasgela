<?php

namespace Tests\Feature\Estructura;

use App\Curso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CursosCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'nombre', 'category_id'
    ];

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.index'));

        // Then
        $response->assertSee($curso->nombre);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('cursos.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('cursos.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('cursos.create'));

        // Then
        $response->assertSeeInOrder([__('New course'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('cursos.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('cursos.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->make();
        $total = Curso::all()->count();

        // When
        $this->post(route('cursos.store'), $curso->toArray());

        // Then
        $this->assertEquals($total + 1, Curso::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = factory(Curso::class)->make();

        // When
        $response = $this->post(route('cursos.store'), $curso->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $curso = factory(Curso::class)->make();

        // When
        $response = $this->post(route('cursos.store'), $curso->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Curso::all()->count();

        $empty = new Curso();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('cursos.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->make([$field => null]);

        // When
        $response = $this->post(route('cursos.store'), $curso->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.show', $curso));

        // Then
        $response->assertStatus(501);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.show', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.show', $curso));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.edit', $curso), $curso->toArray());

        // Then
        $response->assertSeeInOrder([$curso->nombre, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.edit', $curso), $curso->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->get(route('cursos.edit', $curso), $curso->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();
        $curso->nombre = "Updated";

        // When
        $this->put(route('cursos.update', $curso), $curso->toArray());

        // Then
        $this->assertDatabaseHas('cursos', ['id' => $curso->id, 'nombre' => $curso->nombre]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = factory(Curso::class)->create();
        $curso->nombre = "Updated";

        // When
        $response = $this->put(route('cursos.update', $curso), $curso->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $curso = factory(Curso::class)->create();
        $curso->nombre = "Updated";

        // When
        $response = $this->put(route('cursos.update', $curso), $curso->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();
        $empty = new Curso();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('cursos.update', $curso), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();
        $curso->$field = null;

        // When
        $response = $this->put(route('cursos.update', $curso), $curso->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->updateRequires($field);
        }
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $this->delete(route('cursos.destroy', $curso));

        // Then
        $this->assertDatabaseMissing('cursos', $curso->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->delete(route('cursos.destroy', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $curso = factory(Curso::class)->create();

        // When
        $response = $this->delete(route('cursos.destroy', $curso));

        // Then
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Recursos\Rubrics;

use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class RubricsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo',
    ];

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create([
            'plantilla' => true,
        ]);
        session(['filtrar_curso_actual' => $rubric->curso_id]);

        // When
        $response = $this->get(route('rubrics.index'));

        // Then
        $response->assertSuccessful()->assertSee($rubric->titulo);
    }

    public function testNotPlantillaNotIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.index'));

        // Then
        $response->assertDontSee($rubric->titulo);
    }

    public function testIndexAdminFiltro()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $rubric = Rubric::factory()->create([
            'plantilla' => true,
        ]);

        // When
        $response = $this->post(route('rubrics.index.filtro', ['curso_id' => $rubric->curso_id]));

        // Then
        $response->assertSuccessful()->assertSee($rubric->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('rubrics.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('rubrics.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('rubrics.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New rubric'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('rubrics.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('rubrics.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->make();
        $total = Rubric::all()->count();

        // When
        $this->post(route('rubrics.store'), $rubric->toArray());

        // Then
        $this->assertEquals($total + 1, Rubric::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rubric = Rubric::factory()->make();

        // When
        $response = $this->post(route('rubrics.store'), $rubric->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $rubric = Rubric::factory()->make();

        // When
        $response = $this->post(route('rubrics.store'), $rubric->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = Rubric::all()->count();

        $empty = new Rubric();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('rubrics.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, Rubric::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->make([$field => null]);

        // When
        $response = $this->post(route('rubrics.store'), $rubric->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.show', $rubric));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Rubric'), $rubric->titulo]);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.show', $rubric));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.show', $rubric));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.edit', $rubric), $rubric->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$rubric->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.edit', $rubric), $rubric->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->get(route('rubrics.edit', $rubric), $rubric->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();
        $rubric->titulo = "Updated";

        // When
        $this->put(route('rubrics.update', $rubric), $rubric->toArray());

        // Then
        $this->assertDatabaseHas('rubrics', ['id' => $rubric->id, 'titulo' => $rubric->titulo]);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rubric = Rubric::factory()->create();
        $rubric->titulo = "Updated";

        // When
        $response = $this->put(route('rubrics.update', $rubric), $rubric->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $rubric = Rubric::factory()->create();
        $rubric->titulo = "Updated";

        // When
        $response = $this->put(route('rubrics.update', $rubric), $rubric->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();
        $empty = new Rubric();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('rubrics.update', $rubric), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();
        $rubric->$field = null;

        // When
        $response = $this->put(route('rubrics.update', $rubric), $rubric->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $this->delete(route('rubrics.destroy', $rubric));

        // Then
        $this->assertDatabaseMissing('rubrics', $rubric->toArray());
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->delete(route('rubrics.destroy', $rubric));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $rubric = Rubric::factory()->create();

        // When
        $response = $this->delete(route('rubrics.destroy', $rubric));

        // Then
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Recursos\Selectors;

use App\Models\Curso;
use App\Models\Selector;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class SelectorsCRUDTest extends TestCase
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
        $selector = Selector::factory()->create();
        session(['filtrar_curso_actual' => $selector->curso_id]);

        // When
        $response = $this->get(route('selectors.index'));

        // Then
        $response->assertSuccessful()->assertSee($selector->titulo);
    }

    public function testIndexAdminFiltro()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->post(route('selectors.index.filtro', ['curso_id' => $selector->curso_id]));

        // Then
        $response->assertSuccessful()->assertSee($selector->titulo);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('selectors.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('selectors.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('selectors.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New selector'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('selectors.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('selectors.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $selector = Selector::factory()->make();
        $total = Selector::all()->count();

        // When
        $this->post(route('selectors.store'), $selector->toArray());

        // Then
        $this->assertEquals($total + 1, Selector::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->make();

        // When
        $response = $this->post(route('selectors.store'), $selector->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $selector = Selector::factory()->make();

        // When
        $response = $this->post(route('selectors.store'), $selector->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $total = Selector::all()->count();
        $empty = new Selector();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('selectors.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, Selector::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $selector = Selector::factory()->make([$field => null]);

        // When
        $response = $this->post(route('selectors.store'), $selector->toArray());

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
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('selectors.show', $selector));

        // Then
        $response->assertSuccessful()->assertSee($selector->titulo);
    }

    public function testNotAdminProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('selectors.show', $selector));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('selectors.show', $selector));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('selectors.edit', $selector));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$selector->titulo, __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('selectors.edit', $selector));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('selectors.edit', $selector));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $selector = Selector::factory()->create();
        $selector->titulo = 'Updated';

        // When
        $this->put(route('selectors.update', $selector), $selector->toArray());

        // Then
        $this->assertDatabaseHas('selectors', ['id' => $selector->id, 'titulo' => 'Updated']);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->create();
        $selector->titulo = 'Updated';

        // When
        $response = $this->put(route('selectors.update', $selector), $selector->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $selector = Selector::factory()->create();
        $selector->titulo = 'Updated';

        // When
        $response = $this->put(route('selectors.update', $selector), $selector->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $selector = Selector::factory()->create();
        $empty = new Selector();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('selectors.update', $selector), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $selector = Selector::factory()->create();
        $selector->$field = null;

        // When
        $response = $this->put(route('selectors.update', $selector), $selector->toArray());

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
        $selector = Selector::factory()->create();

        // When
        $this->delete(route('selectors.destroy', $selector));

        // Then
        $this->assertDatabaseMissing('selectors', ['id' => $selector->id]);
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->delete(route('selectors.destroy', $selector));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->delete(route('selectors.destroy', $selector));

        // Then
        $response->assertRedirect(route('login'));
    }
}

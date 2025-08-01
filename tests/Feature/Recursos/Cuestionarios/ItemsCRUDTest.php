<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use Override;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ItemsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'texto', 'pregunta_id'
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
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.index'));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('items.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('items.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('items.create'));

        // Then
        $response->assertStatus(404);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('items.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('items.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->make();
        $total = Item::all()->count();

        // When
        $this->post(route('items.store'), $item->toArray());

        // Then
        $this->assertEquals($total + 1, Item::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $item = Item::factory()->make();

        // When
        $response = $this->post(route('items.store'), $item->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $item = Item::factory()->make();

        // When
        $response = $this->post(route('items.store'), $item->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = Item::all()->count();

        $empty = new Item();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('items.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->make([$field => null]);

        // When
        $response = $this->post(route('items.store'), $item->toArray());

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
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.show', $item));

        // Then
        $response->assertStatus(404);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.show', $item));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.show', $item));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.edit', $item), $item->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$item->texto, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.edit', $item), $item->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->get(route('items.edit', $item), $item->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create();
        $item->texto = "Updated";

        // When
        $this->put(route('items.update', $item), $item->toArray());

        // Then
        $this->assertDatabaseHas('items', ['id' => $item->id, 'texto' => $item->texto]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $item = Item::factory()->create();
        $item->texto = "Updated";

        // When
        $response = $this->put(route('items.update', $item), $item->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $item = Item::factory()->create();
        $item->texto = "Updated";

        // When
        $response = $this->put(route('items.update', $item), $item->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create();
        $empty = new Item();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('items.update', $item), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $item = Item::factory()->create();
        $item->$field = null;

        // When
        $response = $this->put(route('items.update', $item), $item->toArray());

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
        $item = Item::factory()->create();

        // When
        $this->delete(route('items.destroy', $item));

        // Then
        $this->assertDatabaseMissing('items', $item->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->delete(route('items.destroy', $item));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $item = Item::factory()->create();

        // When
        $response = $this->delete(route('items.destroy', $item));

        // Then
        $response->assertRedirect(route('login'));
    }
}

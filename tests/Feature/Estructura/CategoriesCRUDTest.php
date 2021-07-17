<?php

namespace Tests\Feature\Estructura;

use App\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoriesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'period_id'
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
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.index'));

        // Then
        $response->assertSee($category->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('categories.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('categories.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('categories.create'));

        // Then
        $response->assertSeeInOrder([__('New category'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('categories.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('categories.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $category = Category::factory()->make();
        $total = Category::all()->count();

        // When
        $this->post(route('categories.store'), $category->toArray());

        // Then
        $this->assertEquals($total + 1, Category::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $category = Category::factory()->make();

        // When
        $response = $this->post(route('categories.store'), $category->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $category = Category::factory()->make();

        // When
        $response = $this->post(route('categories.store'), $category->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Category::all()->count();

        $empty = new Category();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('categories.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $category = Category::factory()->make([$field => null]);

        // When
        $response = $this->post(route('categories.store'), $category->toArray());

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
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.show', $category));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.show', $category));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.show', $category));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.edit', $category), $category->toArray());

        // Then
        $response->assertSeeInOrder([$category->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.edit', $category), $category->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->get(route('categories.edit', $category), $category->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $category = Category::factory()->create();
        $category->name = "Updated";

        // When
        $this->put(route('categories.update', $category), $category->toArray());

        // Then
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => $category->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $category = Category::factory()->create();
        $category->name = "Updated";

        // When
        $response = $this->put(route('categories.update', $category), $category->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $category = Category::factory()->create();
        $category->name = "Updated";

        // When
        $response = $this->put(route('categories.update', $category), $category->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $category = Category::factory()->create();
        $empty = new Category();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('categories.update', $category), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $category = Category::factory()->create();
        $category->$field = null;

        // When
        $response = $this->put(route('categories.update', $category), $category->toArray());

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
        $category = Category::factory()->create();

        // When
        $this->delete(route('categories.destroy', $category));

        // Then
        $this->assertDatabaseMissing('categories', $category->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->delete(route('categories.destroy', $category));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $category = Category::factory()->create();

        // When
        $response = $this->delete(route('categories.destroy', $category));

        // Then
        $response->assertRedirect(route('login'));
    }
}

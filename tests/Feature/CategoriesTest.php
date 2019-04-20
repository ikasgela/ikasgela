<?php

namespace Tests\Feature;

use App\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();

        // When
        $response = $this->get(route('categories.index'));

        // Then
        $response->assertSee($category->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('categories.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('categories.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('categories.create'));

        // Then
        $response->assertSeeInOrder([__('New category'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('categories.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('categories.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->make();

        // When
        $this->post(route('categories.store'), $category->toArray());

        // Then
        $this->assertEquals(1, Category::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $category = factory(Category::class)->make();

        // When
        // Then
        $this->post(route('categories.store'), $category->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $category = factory(Category::class)->make();

        // When
        // Then
        $this->post(route('categories.store'), $category->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->make(['name' => null]);

        // When
        // Then
        $this->post(route('categories.store'), $category->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testStoreRequiresPeriod()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->make(['period_id' => null]);

        // When
        // Then
        $this->post(route('categories.store'), $category->toArray())
            ->assertSessionHasErrors('period_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();

        // When
        $response = $this->get(route('categories.show', ['id' => $category->id]));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $category = factory(Category::class)->create();

        // When
        // Then
        $this->get(route('categories.show', ['id' => $category->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $category = factory(Category::class)->create();

        // When
        // Then
        $this->get(route('categories.show', ['id' => $category->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();

        // When
        $response = $this->get(route('categories.edit', ['id' => $category->id]), $category->toArray());

        // Then
        $response->assertSeeInOrder([$category->name, $category->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $category = factory(Category::class)->create();

        // When
        // Then
        $this->get(route('categories.edit', ['id' => $category->id]), $category->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $category = factory(Category::class)->create();

        // When
        // Then
        $this->get(route('categories.edit', ['id' => $category->id]), $category->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();
        $category->name = "Updated";

        // When
        $this->put(route('categories.update', ['id' => $category->id]), $category->toArray());

        // Then
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => $category->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $category = factory(Category::class)->create();
        $category->name = "Updated";

        // When
        // Then
        $this->put(route('categories.update', ['id' => $category->id]), $category->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $category = factory(Category::class)->create();
        $category->name = "Updated";

        // When
        // Then
        $this->put(route('categories.update', ['id' => $category->id]), $category->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();

        // When
        $category->name = null;

        // Then
        $this->put(route('categories.update', ['id' => $category->id]), $category->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresPeriod()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();

        // When
        $category->period_id = null;

        // Then
        $this->put(route('categories.update', ['id' => $category->id]), $category->toArray())
            ->assertSessionHasErrors('period_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $category = factory(Category::class)->create();

        // When
        $this->delete(route('categories.destroy', ['id' => $category->id]));

        // Then
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $category = factory(Category::class)->create();

        // When
        // Then
        $this->delete(route('categories.destroy', ['id' => $category->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $category = factory(Category::class)->create();

        // When
        // Then
        $this->delete(route('categories.destroy', ['id' => $category->id]))
            ->assertRedirect(route('login'));
    }
}

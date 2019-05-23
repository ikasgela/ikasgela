<?php

namespace Tests\Feature;

use App\MarkdownText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkdownTextsTest extends TestCase
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
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $response = $this->get(route('markdown_texts.index'));

        // Then
        $response->assertSee($markdown_text->name);
    }

    public function testNotProfesorNotIndex()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('markdown_texts.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('markdown_texts.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('markdown_texts.create'));

        // Then
        $response->assertSeeInOrder([__('New markdown text'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Given
        $this->actingAs($this->not_profesor);

        // When
        // Then
        $this->get(route('markdown_texts.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('markdown_texts.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->make();

        // When
        $this->post(route('markdown_texts.store'), $markdown_text->toArray());

        // Then
        $this->assertEquals(1, MarkdownText::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $markdown_text = factory(MarkdownText::class)->make();

        // When
        // Then
        $this->post(route('markdown_texts.store'), $markdown_text->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $markdown_text = factory(MarkdownText::class)->make();

        // When
        // Then
        $this->post(route('markdown_texts.store'), $markdown_text->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->make(['titulo' => null]);

        // When
        // Then
        $this->post(route('markdown_texts.store'), $markdown_text->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testStoreRequiresRepositorio()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->make(['repositorio' => null]);

        // When
        // Then
        $this->post(route('markdown_texts.store'), $markdown_text->toArray())
            ->assertSessionHasErrors('repositorio');
    }

    public function testStoreRequiresArchivo()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->make(['archivo' => null]);

        // When
        // Then
        $this->post(route('markdown_texts.store'), $markdown_text->toArray())
            ->assertSessionHasErrors('archivo');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $response = $this->get(route('markdown_texts.show', ['id' => $markdown_text->id]));

        // Then
        $response->assertSeeInOrder([__('Markdown text'), $markdown_text->titulo]);
    }

    public function testNotProfesorNotShow()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        // Then
        $this->get(route('markdown_texts.show', ['id' => $markdown_text->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        // Then
        $this->get(route('markdown_texts.show', ['id' => $markdown_text->id]))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $response = $this->get(route('markdown_texts.edit', ['id' => $markdown_text->id]), $markdown_text->toArray());

        // Then
        $response->assertSeeInOrder([$markdown_text->titulo, $markdown_text->repositorio, $markdown_text->archivo, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        // Then
        $this->get(route('markdown_texts.edit', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        // Then
        $this->get(route('markdown_texts.edit', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();
        $markdown_text->titulo = "Updated";

        // When
        $this->put(route('markdown_texts.update', ['id' => $markdown_text->id]), $markdown_text->toArray());

        // Then
        $this->assertDatabaseHas('markdown_texts', ['id' => $markdown_text->id, 'titulo' => $markdown_text->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $markdown_text = factory(MarkdownText::class)->create();
        $markdown_text->titulo = "Updated";

        // When
        // Then
        $this->put(route('markdown_texts.update', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $markdown_text = factory(MarkdownText::class)->create();
        $markdown_text->titulo = "Updated";

        // When
        // Then
        $this->put(route('markdown_texts.update', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresTitulo()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $markdown_text->titulo = null;

        // Then
        $this->put(route('markdown_texts.update', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertSessionHasErrors('titulo');
    }

    public function testUpdateRequiresRepositorio()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $markdown_text->repositorio = null;

        // Then
        $this->put(route('markdown_texts.update', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertSessionHasErrors('repositorio');
    }

    public function testUpdateRequiresArchivo()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $markdown_text->archivo = null;

        // Then
        $this->put(route('markdown_texts.update', ['id' => $markdown_text->id]), $markdown_text->toArray())
            ->assertSessionHasErrors('archivo');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        $this->delete(route('markdown_texts.destroy', ['id' => $markdown_text->id]));

        // Then
        $this->assertDatabaseMissing('markdown_texts', ['id' => $markdown_text->id]);
    }

    public function testNotProfesorNotDelete()
    {
        // Given
        $this->actingAs($this->not_profesor);
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        // Then
        $this->delete(route('markdown_texts.destroy', ['id' => $markdown_text->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $markdown_text = factory(MarkdownText::class)->create();

        // When
        // Then
        $this->delete(route('markdown_texts.destroy', ['id' => $markdown_text->id]))
            ->assertRedirect(route('login'));
    }
}

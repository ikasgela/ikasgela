<?php

namespace Tests\Feature\Recursos\MarkdownTexts;

use App\Models\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MarkdownTextsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo', 'repositorio', 'archivo'
    ];

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
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.index'));

        // Then
        $response->assertSee($markdown_text->titulo);
    }

    public function testIndexAdminFiltro()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->post(route('markdown_texts.index.filtro', ['curso_id' => $markdown_text->curso_id]));

        // Then
        $response->assertSee($markdown_text->titulo);
    }

    public function testNotProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('markdown_texts.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('markdown_texts.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('markdown_texts.create'));

        // Then
        $response->assertSeeInOrder([__('New markdown text'), __('Save')]);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('markdown_texts.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('markdown_texts.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->make();
        $total = MarkdownText::all()->count();

        // When
        $this->post(route('markdown_texts.store'), $markdown_text->toArray());

        // Then
        $this->assertEquals($total + 1, MarkdownText::all()->count());
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->make();

        // When
        $response = $this->post(route('markdown_texts.store'), $markdown_text->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $markdown_text = MarkdownText::factory()->make();

        // When
        $response = $this->post(route('markdown_texts.store'), $markdown_text->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = MarkdownText::all()->count();

        $empty = new MarkdownText();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $this->post(route('markdown_texts.store'), $empty->toArray());

        // Then
        $this->assertCount($total + 1, MarkdownText::all());
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->make([$field => null]);

        // When
        $response = $this->post(route('markdown_texts.store'), $markdown_text->toArray());

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
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.show', $markdown_text));

        // Then
        $response->assertSeeInOrder([__('Markdown text'), $markdown_text->titulo]);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.show', $markdown_text));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.show', $markdown_text));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.edit', $markdown_text), $markdown_text->toArray());

        // Then
        $response->assertSeeInOrder([$markdown_text->titulo, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.edit', $markdown_text), $markdown_text->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.edit', $markdown_text), $markdown_text->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();
        $markdown_text->titulo = "Updated";

        // When
        $this->put(route('markdown_texts.update', $markdown_text), $markdown_text->toArray());

        // Then
        $this->assertDatabaseHas('markdown_texts', ['id' => $markdown_text->id, 'titulo' => $markdown_text->titulo]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();
        $markdown_text->titulo = "Updated";

        // When
        $response = $this->put(route('markdown_texts.update', $markdown_text), $markdown_text->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $markdown_text = MarkdownText::factory()->create();
        $markdown_text->titulo = "Updated";

        // When
        $response = $this->put(route('markdown_texts.update', $markdown_text), $markdown_text->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();
        $empty = new MarkdownText();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('markdown_texts.update', $markdown_text), $empty->toArray());

        // Then
        $response->assertSessionDoesntHaveErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();
        $markdown_text->$field = null;

        // When
        $response = $this->put(route('markdown_texts.update', $markdown_text), $markdown_text->toArray());

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
        $markdown_text = MarkdownText::factory()->create();

        // When
        $this->delete(route('markdown_texts.destroy', $markdown_text));

        // Then
        $this->assertDatabaseMissing('markdown_texts', $markdown_text->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->delete(route('markdown_texts.destroy', $markdown_text));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->delete(route('markdown_texts.destroy', $markdown_text));

        // Then
        $response->assertRedirect(route('login'));
    }
}

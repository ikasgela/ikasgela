<?php

namespace Tests\Feature\Recursos\MarkdownTexts;

use Override;
use App\Models\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MarkdownTextsExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();
        $count = MarkdownText::count();

        // When
        $response = $this->post(route('markdown_texts.duplicar', $markdown_text));

        // Then
        $response->assertRedirect(route('markdown_texts.index'));
        $this->assertSame($count + 1, MarkdownText::count());
    }

    public function testBorrarCache()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.borrar_cache', $markdown_text));

        // Then
        $response->assertRedirect();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->post(route('markdown_texts.duplicar', $markdown_text));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotBorrarCache()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $markdown_text = MarkdownText::factory()->create();

        // When
        $response = $this->get(route('markdown_texts.borrar_cache', $markdown_text));

        // Then
        $response->assertForbidden();
    }
}

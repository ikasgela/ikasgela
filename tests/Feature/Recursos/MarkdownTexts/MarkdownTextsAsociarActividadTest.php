<?php

namespace Tests\Feature\Recursos\MarkdownTexts;

use App\Actividad;
use App\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MarkdownTextsAsociarActividadTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testActividad()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $markdown_text1 = MarkdownText::factory()->create();
        $markdown_text2 = MarkdownText::factory()->create();
        $markdown_text3 = MarkdownText::factory()->create();

        $actividad->markdown_texts()->attach($markdown_text1);
        $actividad->markdown_texts()->attach($markdown_text3);

        // When
        $response = $this->get(route('markdown_texts.actividad', $actividad));

        // Then
        $response->assertSeeInOrder([
            __('Resources: Markdown texts'),
            __('Assigned resources'),
            $markdown_text1->repositorio,
            $markdown_text3->repositorio,
            __('Available resources'),
            $markdown_text2->repositorio,
        ]);
    }

    public function testAsociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $markdown_text1 = MarkdownText::factory()->create();
        $markdown_text2 = MarkdownText::factory()->create();

        // When
        $this->post(route('markdown_texts.asociar', $actividad), ['seleccionadas' => [$markdown_text1, $markdown_text2]]);

        // Then
        $this->assertCount(2, $actividad->markdown_texts()->get());
    }

    public function testAsociarRequiresSeleccionadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('markdown_texts.asociar', $actividad), ['seleccionadas' => null]);

        // Then
        $response->assertSessionHasErrors('seleccionadas');
    }

    public function testDesasociar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();
        $markdown_text1 = MarkdownText::factory()->create();
        $markdown_text2 = MarkdownText::factory()->create();

        $actividad->markdown_texts()->attach($markdown_text1);
        $actividad->markdown_texts()->attach($markdown_text2);

        // When
        $this->delete(route('markdown_texts.desasociar', [$actividad, $markdown_text1]));

        // Then
        $this->assertCount(1, $actividad->markdown_texts()->get());
    }
}

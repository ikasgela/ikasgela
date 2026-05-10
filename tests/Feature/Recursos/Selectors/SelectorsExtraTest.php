<?php

namespace Tests\Feature\Recursos\Selectors;

use App\Models\Selector;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class SelectorsExtraTest extends TestCase
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
        $selector = Selector::factory()->create();
        $total = Selector::all()->count();

        // When
        $this->post(route('selectors.duplicar', $selector));

        // Then
        $this->assertEquals($total + 1, Selector::all()->count());
    }

    public function testNotAdminProfesorNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->post(route('selectors.duplicar', $selector));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->post(route('selectors.duplicar', $selector));

        // Then
        $response->assertRedirect(route('login'));
    }
}

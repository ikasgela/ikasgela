<?php

namespace Tests\Feature\Usuarios;

use Tests\TestCase;

class CheckBlockedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function unblocked_user_can_login()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $this->alumno->blocked_date = null;

        // When
        $response = $this->get(route('users.home'));

        // Then
        $response->assertSuccessful()->assertSee(__('Desktop'));
    }

    /** @test */
    public function blocked_user_cannot_login()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $this->alumno->blocked_date = now()->addDays(-1);

        // When
        $response = $this->get(route('users.home'));

        // Then
        $response->assertRedirect(route('blocked'));
    }
}

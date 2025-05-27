<?php

namespace Tests\Feature\Usuarios;

use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckBlockedTest extends TestCase
{
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    #[Test]
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

    #[Test]
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

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndexProfesorRedirectsToProfesor()
    {
        // Auth
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('portada'));

        // Then
        $response->assertRedirect(route('profesor.index'));
    }

    public function testIndexAdminRedirectsToAdmin()
    {
        // Auth
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('portada'));

        // Then
        $response->assertRedirect(route('admin.index'));
    }

    public function testIndexTutorRedirectsToTutor()
    {
        // Auth
        $this->actingAs($this->tutor);

        // When
        $response = $this->get(route('portada'));

        // Then
        $response->assertRedirect(route('tutor.index'));
    }

    public function testIndexAlumnoRedirectsToUsersHome()
    {
        // Auth
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('portada'));

        // Then
        $response->assertRedirect(route('users.home'));
    }

    public function testPortadaAsAdminRedirectsToAdmin()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('portada'));

        $response->assertRedirect(route('admin.index'));
    }
}

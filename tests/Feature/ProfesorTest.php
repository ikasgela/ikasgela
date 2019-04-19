<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfesorTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testPanelControl()
    {
        $user = User::find(3);  // Profesor

        $response = $this
            ->actingAs($user)
            ->get('/alumnos')
            ->assertStatus(200);
    }

    public function testTareasAlumno()
    {
        $user = User::find(3);

        $response = $this
            ->actingAs($user)
            ->get('/alumnos/1/tareas')
            ->assertStatus(200);
    }

    public function testRevisarActividad()
    {
        $user = User::find(3);

        $response = $this
            ->actingAs($user)
            ->get('/profesor/1/revisar/1')
            ->assertStatus(200);
    }

    public function testPrevisualizarActividad()
    {
        $user = User::find(3);

        $response = $this
            ->actingAs($user)
            ->get('/actividades/1/preview')
            ->assertStatus(200);
    }
}

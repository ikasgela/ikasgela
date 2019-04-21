<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfesorTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        parent::crearUsuarios();
    }

    public function testPanelControl()
    {
        $this->actingAs($this->profesor)
            ->get('/alumnos')
            ->assertStatus(200);
    }

    public function testTareasAlumno()
    {
        $this->actingAs($this->profesor)
            ->get('/alumnos/1/tareas')
            ->assertStatus(200);
    }

    public function testRevisarActividad()
    {
        $this->actingAs($this->profesor)
            ->get('/profesor/1/revisar/1')
            ->assertStatus(200);
    }

    public function testPrevisualizarActividad()
    {
        $this->actingAs($this->profesor)
            ->get('/actividades/1/preview')
            ->assertStatus(200);
    }
}

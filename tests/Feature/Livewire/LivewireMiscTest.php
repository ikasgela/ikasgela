<?php

namespace Tests\Feature\Livewire;

use App\Livewire\BotonAccion;
use App\Livewire\ExportarUsuario;
use App\Models\Actividad;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireMiscTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearUsuarios();
    }

    public function testBotonAccionRender()
    {
        $actividad = Actividad::factory()->create();
        $tarea = Tarea::factory()->create([
            'user_id' => $this->alumno->id,
            'actividad_id' => $actividad->id,
        ]);

        Livewire::actingAs($this->alumno)
            ->test(BotonAccion::class, [
                'actividad' => $actividad,
                'tarea' => $tarea,
            ])
            ->assertOk();
    }

    public function testExportarUsuarioRenderNoExport()
    {
        Livewire::actingAs($this->alumno)
            ->test(ExportarUsuario::class)
            ->assertOk()
            ->assertSet('exporting', false)
            ->assertSet('already_exported', false);
    }

    public function testExportarUsuarioExport()
    {
        Livewire::actingAs($this->alumno)
            ->test(ExportarUsuario::class)
            ->call('export')
            ->assertSet('exporting', true);
    }
}

<?php

namespace Tests\Browser\Sitio;

use App\Models\Cuestionario;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Phase 5: Admin CRUD for cuestionarios (questionnaires).
 * Tests create/edit/delete of questionnaires only (question management
 * is complex due to inline form handling and is deferred to Phase 6).
 */
class T10_CuestionarioCRUDTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private static ?int $cuestionarioId = null;

    // --- Login ---

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'admin@ikasgela.com', '12345Abcde', 'admin.index');
        });
    }

    // --- Cuestionario CRUD ---

    public function testCrearCuestionario(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('cuestionarios.index'));
            $browser->visit(route('cuestionarios.create'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('New questionnaire'));

            $browser->type('titulo', '__Dusk_Q10__');
            $browser->type('descripcion', 'Cuestionario de test Dusk');
            $browser->check('plantilla');
            $browser->press(__('Save'));

            // store action redirects to edit page
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Edit questionnaire'));
            $browser->assertInputValue('titulo', '__Dusk_Q10__');

            self::$cuestionarioId = Cuestionario::where('titulo', '__Dusk_Q10__')->value('id');
            $this->assertNotNull(self::$cuestionarioId, 'Cuestionario should be created');
        });
    }

    public function testEditarCuestionario(): void
    {
        $this->browse(function (Browser $browser) {
            // Visit index first so retornar() redirects back there after update
            $browser->visit(route('cuestionarios.index'));
            $browser->visit(route('cuestionarios.edit', self::$cuestionarioId));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Edit questionnaire'));

            $browser->clear('titulo');
            $browser->type('titulo', '__Dusk_Q10_Editado__');
            $browser->clear('descripcion');
            $browser->type('descripcion', 'Editado desde Dusk');
            $browser->press(__('Save'));

            $browser->assertRouteIs('cuestionarios.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_Q10_Editado__');
        });
    }

    public function testEliminarCuestionario(): void
    {
        $this->browse(function (Browser $browser) {
            $cuestionario = Cuestionario::findOrFail(self::$cuestionarioId);

            $browser->visit(route('cuestionarios.index'));
            $this->assertNoAppErrors($browser);

            // El botón borrar muestra un modal de confirmación; usar JS para hacer clic
            $browser->script(
                "document.querySelector(\"form[action='" . route('cuestionarios.destroy', $cuestionario) . "']\").querySelector('[name=borrar]').click();"
            );
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('cuestionarios.index');
            $this->assertNoAppErrors($browser);
            $browser->assertDontSee('__Dusk_Q10_Editado__');
        });
    }

    // --- Teardown safety ---

    public function testTeardown(): void
    {
        if (self::$cuestionarioId) {
            Cuestionario::find(self::$cuestionarioId)?->delete();
        }
        $this->assertTrue(true);
    }

    // --- Logout ---

    public function testLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }
}

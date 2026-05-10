<?php

namespace Tests\Browser\Sitio;

use App\Models\Organization;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * CRUD real del administrador:
 * - Crear, editar y eliminar una organización
 * - Crear, editar y eliminar un usuario
 */
class T7_AdminCRUDTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private static ?int $orgId = null;
    private static ?int $userId = null;

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'admin@ikasgela.com', '12345Abcde', 'admin.index');
        });
    }

    // -------------------------------------------------------------------------
    // Organizaciones
    // -------------------------------------------------------------------------

    public function testCrearOrganizacion(): void
    {
        $this->browse(function (Browser $browser) {
            // Visitar el índice primero para que retornar() tenga historial correcto
            $browser->visit(route('organizations.index'));
            $browser->visit(route('organizations.create'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('New organization'));

            $browser->type('name', 'Org Dusk Test');
            $browser->type('slug', 'org-dusk-test');
            $browser->type('seats', '30');
            $browser->press(__('Save'));

            $browser->assertRouteIs('organizations.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('Org Dusk Test');
        });

        static::$orgId = Organization::where('name', 'Org Dusk Test')->value('id');
    }

    public function testEditarOrganizacion(): void
    {
        $this->assertNotNull(static::$orgId, 'No se creó la organización en el test anterior');
        $org = Organization::findOrFail(static::$orgId);

        $this->browse(function (Browser $browser) use ($org) {
            // Visitar el índice primero para que retornar() tenga historial correcto
            $browser->visit(route('organizations.index'));
            $browser->visit(route('organizations.edit', $org));
            $this->assertNoAppErrors($browser);

            $browser->clear('name');
            $browser->type('name', 'Org Dusk Editada');
            $browser->press(__('Save'));

            $browser->assertRouteIs('organizations.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('Org Dusk Editada');
        });
    }

    public function testEliminarOrganizacion(): void
    {
        $this->assertNotNull(static::$orgId, 'No se creó la organización en el test anterior');
        $org = Organization::findOrFail(static::$orgId);

        $this->browse(function (Browser $browser) use ($org) {
            $browser->visit(route('organizations.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee('Org Dusk Editada');

            // El botón borrar muestra un modal de confirmación; usar JS para hacer clic
            $browser->script(
                "document.querySelector(\"form[action='" . route('organizations.destroy', $org) . "']\").querySelector('[name=borrar]').click();"
            );
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('organizations.index');
            $this->assertNoAppErrors($browser);
            $browser->assertDontSee('Org Dusk Editada');
        });

        static::$orgId = null;
    }

    // -------------------------------------------------------------------------
    // Usuarios
    // -------------------------------------------------------------------------

    public function testCrearUsuario(): void
    {
        $this->browse(function (Browser $browser) {
            // Visitar el índice primero para que retornar() tenga historial correcto
            $browser->visit(route('users.index'));
            $browser->visit(route('users.create'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('New user'));

            $browser->type('name', 'Alumno');
            $browser->type('surname', 'Dusk Test');
            $browser->type('email', 'alumno.dusk@test.com');
            $browser->type('password', '12345Abcde');
            // El rol "Alumno" tiene id=3
            $browser->check('[name="roles_seleccionados[]"][value="3"]');
            $browser->press(__('Save'));

            $browser->assertRouteIs('users.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('Alumno');
        });

        static::$userId = User::where('email', 'alumno.dusk@test.com')->value('id');
    }

    public function testEditarUsuario(): void
    {
        $this->assertNotNull(static::$userId, 'No se creó el usuario en el test anterior');
        $user = User::findOrFail(static::$userId);

        $this->browse(function (Browser $browser) use ($user) {
            // Visitar el índice primero para que retornar() tenga historial correcto
            $browser->visit(route('users.index'));
            $browser->visit(route('users.edit', $user));
            $this->assertNoAppErrors($browser);

            $browser->clear('name');
            $browser->type('name', 'AlumnoDusk');
            $browser->press(__('Save'));

            $browser->assertRouteIs('users.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('AlumnoDusk');
        });
    }

    public function testEliminarUsuario(): void
    {
        $this->assertNotNull(static::$userId, 'No se creó el usuario en el test anterior');
        $user = User::findOrFail(static::$userId);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('users.index'));
            $this->assertNoAppErrors($browser);

            // El botón borrar muestra un modal de confirmación; usar JS para hacer clic
            $browser->script(
                "document.querySelector(\"form[action='" . route('users.destroy', $user) . "']\").querySelector('[name=borrar]').click();"
            );
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('users.index');
            $this->assertNoAppErrors($browser);
        });

        // La eliminación se despacha como job asíncrono (QUEUE_CONNECTION=redis);
        // limpiar directamente para no dejar datos residuales
        $user->delete();
        static::$userId = null;
    }

    public function testLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }
}

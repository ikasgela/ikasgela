<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Phase 4: Permission boundary tests.
 * Verifies that role-based middleware denies access (403) when
 * a user attempts to reach a route outside their role.
 */
class T9_PermisosAccesoTest extends DuskTestCase
{
    use BrowserUiHelpers;

    // --- Alumno cannot access admin/profesor routes ---

    public function testAlumnoNoAccedeAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');

            $browser->visit(route('admin.index'));
            $browser->assertSee('403');
        });
    }

    public function testAlumnoNoAccedeProfesor(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('profesor.index'));
            $browser->assertSee('403');
        });
    }

    public function testAlumnoNoAccedeGestionUsuarios(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.index'));
            $browser->assertSee('403');
        });
    }

    public function testAlumnoLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }

    // --- Profesor cannot access admin-only or alumno-only routes ---

    public function testProfesorNoAccedeAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'profesor@ikasgela.com', '12345Abcde', 'profesor.index');

            $browser->visit(route('admin.index'));
            $browser->assertSee('403');
        });
    }

    public function testProfesorNoAccedeEscritorioAlumno(): void
    {
        $this->browse(function (Browser $browser) {
            // users.home is role:alumno only
            $browser->visit(route('users.home'));
            $browser->assertSee('403');
        });
    }

    public function testProfesorLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }

    // --- Tutor cannot access admin-only routes ---

    public function testTutorNoAccedeAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'tutor@ikasgela.com', '12345Abcde', 'tutor.index');

            $browser->visit(route('admin.index'));
            $browser->assertSee('403');
        });
    }

    public function testTutorNoAccedeProfesor(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('profesor.index'));
            $browser->assertSee('403');
        });
    }

    public function testTutorLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }
}

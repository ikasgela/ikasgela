<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T5_AdministradorTest extends DuskTestCase
{
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'));
            $browser->type('email', 'admin@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('admin.index');
            $browser->assertDontSee('Ignition');
            $browser->assertDontSee('403');
        });
    }

    public function testActividadesPlantillas()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('actividades.plantillas'));
            $browser->assertRouteIs('actividades.plantillas');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities'));
        });
    }

    public function testActividadesClonador()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('intellij_projects.copia'));
            $browser->assertRouteIs('intellij_projects.copia');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Project cloner'));
        });
    }

    public function testEstructuraOrganizaciones()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('organizations.index'));
            $browser->assertRouteIs('organizations.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Organizations'));
        });
    }

    public function testEstructuraPeriodos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('periods.index'));
            $browser->assertRouteIs('periods.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Periods'));
        });
    }

    public function testEstructuraCategorias()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('categories.index'));
            $browser->assertRouteIs('categories.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Categories'));
        });
    }

    public function testEstructuraCursos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('cursos.index'));
            $browser->assertRouteIs('cursos.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Courses'));
        });
    }

    public function testEstructuraUnidades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('unidades.index'));
            $browser->assertRouteIs('unidades.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Units'));
        });
    }

    public function testEstructuraActividades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('actividades.index'));
            $browser->assertRouteIs('actividades.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities'));
        });
    }

    public function testUsuariosGrupos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('groups.index'));
            $browser->assertRouteIs('groups.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Groups'));
        });
    }

    public function testUsuariosEquipos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('teams.index'));
            $browser->assertRouteIs('teams.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Teams'));
        });
    }

    public function testUsuariosUsuarios()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.index'));
            $browser->assertRouteIs('users.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Users'));
        });
    }

    public function testUsuariosRoles()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('roles.index'));
            $browser->assertRouteIs('roles.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Roles'));
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->visit(route('portada'));
            $browser->assertRouteIs('portada');
            $browser->assertDontSee('Ignition');
        });
    }
}

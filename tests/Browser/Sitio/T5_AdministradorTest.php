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

    public function testEstructuraActividades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('actividades.index'));
            $browser->assertRouteIs('actividades.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities'));
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

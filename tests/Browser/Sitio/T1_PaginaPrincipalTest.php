<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T1_PaginaPrincipalTest extends DuskTestCase
{
    public function testPaginaPrincipal()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('portada'));
            $browser->assertTitle('ikasgela');
            $browser->assertDontSee('Ignition');
            $browser->assertDontSee('403');
            $browser->assertDontSee('404');
        });
    }
}

<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T1_PaginaPrincipalTest extends DuskTestCase
{
    public function testPaginaPrincipal()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertTitle('ikasgela');
        });
    }
}

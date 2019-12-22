<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaginaPrincipalTest extends DuskTestCase
{
    public function testPaginaPrincipal()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertTitle('ikasgela');
        });
    }
}

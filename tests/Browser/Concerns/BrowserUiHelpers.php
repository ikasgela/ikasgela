<?php

namespace Tests\Browser\Concerns;

use Laravel\Dusk\Browser;

trait BrowserUiHelpers
{
    protected function loginAs(Browser $browser, string $email, string $password, string $expectedRoute): void
    {
        $browser->visit(route('login'))
            ->type('email', $email)
            ->type('password', $password)
            ->check('remember')
            ->press(__('Login'))
            ->assertRouteIs($expectedRoute);

        $this->assertNoAppErrors($browser);
    }

    protected function logoutToPortada(Browser $browser): void
    {
        $browser->logout()
            ->visit(route('portada'))
            ->assertRouteIs('portada');

        $this->assertNoAppErrors($browser, false);
    }

    protected function assertNoAppErrors(Browser $browser, bool $assertHttpCodes = true): void
    {
        $browser->assertDontSee('Ignition');

        if ($assertHttpCodes) {
            $browser->assertDontSee('403');
            $browser->assertDontSee('404');
        }
    }
}


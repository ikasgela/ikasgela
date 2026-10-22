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

    protected function assertNoAppErrors(Browser $browser, bool $assertPaginaDeError = true): void
    {
        $browser->assertDontSee('Ignition');

        if ($assertPaginaDeError) {
            // Las páginas de error (403, 404, 500...) usan su propia maquetación.
            // Se comprueba por estructura porque el texto depende del idioma y
            // los códigos sueltos (403, 404) pueden aparecer en cualquier sitio.
            $browser->assertScript("!document.querySelector('.font-black.text-5xl')");
        }
    }
}


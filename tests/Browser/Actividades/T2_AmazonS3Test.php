<?php

namespace Tests\Browser\Actividades;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T2_AmazonS3Test extends DuskTestCase
{
    public function testSubirImagen()
    {
        $this->browse(function (Browser $browser) {

            // Login de profesor
            $browser->visit(route('login'));
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('profesor.index');

            // Abrir la actividad de subida de archivo
            $browser->visit('/file_uploads/1');
            $browser->assertSee(__('Image upload'));

            // Subir el archivo
            $browser->attach('files[]', base_path("public/icons_debug/test.png"));
            $browser->press(__('Upload'));
            $browser->assertSee(formato_decimales(10.49, 2) . ' KB');

            // Borrar el fichero
            $browser->press('borrar');
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->assertSee(__('Confirm'));
            $browser->press(__('Confirm'));

            // Se vuelve a mostrar el botón de subir fichero
            $browser->assertDontSee(formato_decimales(10.49, 2) . ' KB');

            // Abrir la actividad de subida de archivo
            $browser->visit('/file_uploads/1');
            $browser->assertSee(__('Image upload'));

            // Subir el archivo
            $browser->attach('files[]', base_path("public/icons_debug/test.jpg"));
            $browser->press(__('Upload'));
            $browser->assertSee(formato_decimales(10.55, 2) . ' KB');

            // Borrar el fichero
            $browser->press('borrar');
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->assertSee(__('Confirm'));
            $browser->press(__('Confirm'));

            // Se vuelve a mostrar el botón de subir fichero
            $browser->assertDontSee(formato_decimales(10.55, 2) . ' KB');
        });
    }
}

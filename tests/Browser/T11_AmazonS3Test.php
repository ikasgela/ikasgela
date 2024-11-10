<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T11_AmazonS3Test extends DuskTestCase
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
            $browser->attach('file', base_path("public/icons_debug/test.png"));
            $browser->press(__('Upload'));
            $browser->assertSee(formato_decimales(38.59, 2) . ' KB');

            // Borrar el fichero
            $browser->press('borrar');
            $browser->acceptDialog();

            // Se vuelve a mostrar el botón de subir fichero
            $browser->assertDontSee(formato_decimales(38.59, 2) . ' KB');

            // Abrir la actividad de subida de archivo
            $browser->visit('/file_uploads/1');
            $browser->assertSee(__('Image upload'));

            // Subir el archivo
            $browser->attach('file', base_path("public/icons_debug/test.jpg"));
            $browser->press(__('Upload'));
            $browser->assertSee(formato_decimales(38.65, 2) . ' KB');

            // Borrar el fichero
            $browser->press('borrar');
            $browser->acceptDialog();

            // Se vuelve a mostrar el botón de subir fichero
            $browser->assertDontSee(formato_decimales(38.65, 2) . ' KB');
        });
    }
}

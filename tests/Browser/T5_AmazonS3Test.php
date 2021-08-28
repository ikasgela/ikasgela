<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T5_AmazonS3Test extends DuskTestCase
{
    public function testSubirImagen()
    {
        $this->browse(function (Browser $browser) {

            // Login de profesor
            $browser->visit('/login');
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/alumnos');

            // Abrir la actividad de subida de archivo
            $browser->visit('/file_uploads/1');
            $browser->assertSee(__('Image upload'));

            // Subir el archivo
            $browser->attach('file', base_path("public/icons_debug/test.png"));
            $browser->press(__('Upload'));
            $browser->assertSee('38,59 KB');

            // Borrar el fichero
            $browser->press('borrar');
            $browser->acceptDialog();

            // Se vuelve a mostrar el botón de subir fichero
            $browser->assertSee(__('Upload'));

            // Abrir la actividad de subida de archivo
            $browser->visit('/file_uploads/1');
            $browser->assertSee(__('Image upload'));

            // Subir el archivo
            $browser->attach('file', base_path("public/icons_debug/test.jpg"));
            $browser->press(__('Upload'));
            $browser->assertSee('38,65 KB');

            // Borrar el fichero
            $browser->visit('/file_uploads/1');
            $browser->press('borrar');
            $browser->acceptDialog();

            // Se vuelve a mostrar el botón de subir fichero
            $browser->visit('/file_uploads/1');
            $browser->assertSee(__('Upload'));
        });
    }
}

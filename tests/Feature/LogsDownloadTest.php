<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Override;
use Tests\TestCase;

/**
 * La descarga de logs (`?dl=`) devuelve un `Symfony BinaryFileResponse`.
 * El middleware `LocaleCookieRedirect` (mcamara/laravel-localization) llama a
 * `withCookie()` sobre la respuesta, un método que no existe en Symfony 8 para
 * clases de Symfony «puras»: solo se dispara cuando la URL lleva el prefijo de un
 * locale no por defecto (p. ej. `/es/logs`), así que el test fuerza
 * `ROUTING_LOCALE=es` para que las rutas se registren con prefijo `/es` y el
 * middleware recorra el mismo camino que en producción.
 */
class LogsDownloadTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        $this->forzarLocaleRutaje('es');

        parent::setUp();
        parent::crearUsuarios();

        App::setLocale('es');
    }

    #[Override]
    protected function tearDown(): void
    {
        $this->liberarLocaleRutaje();
        File::delete(storage_path('logs/prueba-descarga.log'));

        parent::tearDown();
    }

    public function testDownloadLogFile(): void
    {
        $this->actingAs($this->admin);

        File::ensureDirectoryExists(storage_path('logs'));
        File::put(storage_path('logs/prueba-descarga.log'), "2026-01-01 12:00:00 info: línea de prueba\n");
        $dl = Crypt::encryptString('prueba-descarga.log');

        $response = $this->get(route('logs') . '?dl=' . $dl);

        $response->assertSuccessful();
    }

    public function testViewLogPage(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('logs'));

        $response->assertSuccessful()->assertSee('Laravel Log Viewer');
    }

    /**
     * Fuerza el locale de registro de rutas (mcamara/laravel-localization) para
     * que el grupo de rutas localizadas se registre con el prefijo `/{locale}`.
     */
    private function forzarLocaleRutaje(string $locale): void
    {
        $_ENV['ROUTING_LOCALE'] = $locale;
        $_SERVER['ROUTING_LOCALE'] = $locale;
        putenv("ROUTING_LOCALE=$locale");
    }

    private function liberarLocaleRutaje(): void
    {
        unset($_ENV['ROUTING_LOCALE'], $_SERVER['ROUTING_LOCALE']);
        putenv('ROUTING_LOCALE');
    }
}

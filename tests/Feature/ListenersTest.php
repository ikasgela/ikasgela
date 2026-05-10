<?php

namespace Tests\Feature;

use App\Listeners\ActivarUsuario;
use App\Listeners\ZipStreamedListener;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Events\ZipStreamed;
use Tests\TestCase;

class ListenersTest extends TestCase
{
    use DatabaseTransactions;

    // --- ZipStreamedListener::handle ---

    public function testZipStreamedListenerHandle()
    {
        Storage::fake('temp');
        Storage::disk('temp')->makeDirectory('my-export-dir');

        session(['_delete_me' => 'my-export-dir']);

        $event = $this->createMock(ZipStreamed::class);

        $listener = new ZipStreamedListener();
        $listener->handle($event);

        // Session key should be forgotten
        $this->assertNull(session('_delete_me'));
    }

    // --- ActivarUsuario::__construct ---

    public function testActivarUsuarioConstruct()
    {
        $listener = new ActivarUsuario();
        $this->assertInstanceOf(ActivarUsuario::class, $listener);
    }

    // --- ActivarUsuario::handle ---

    public function testActivarUsuarioHandle()
    {
        // Disable Gitea so GiteaClient is not called
        config(['ikasgela.gitea_enabled' => false]);

        $this->crearUsuarios();
        $this->actingAs($this->alumno);

        $event = new Verified($this->alumno);

        $listener = new ActivarUsuario();
        $listener->handle($event);

        // Just assert that the method ran without throwing
        $this->assertTrue(true);
    }
}

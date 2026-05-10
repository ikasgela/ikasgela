<?php

namespace Tests\Unit;

use App\Mail\ActividadAsignada;
use App\Mail\Alerta;
use App\Mail\ExportCompletado;
use App\Mail\FeedbackRecibido;
use App\Mail\NotificationTest;
use App\Mail\NuevoMensaje;
use App\Mail\NuevoUsuario;
use App\Mail\PlazoAmpliado;
use App\Mail\RepositorioClonado;
use App\Mail\RepositorioClonadoError;
use App\Mail\TareaEnviada;
use App\Models\Hilo;
use App\Models\Tarea;
use App\Models\User;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class MailTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testActividadAsignada()
    {
        $mailable = new ActividadAsignada('John', '- Unit 1 - Activity A.');

        $mailable->assertHasSubject(__('New activities assigned'));
        $mailable->build();
    }

    public function testAlerta()
    {
        $user = User::factory()->create();
        $hilo = Hilo::create(['subject' => 'Test', 'owner_id' => $user->id, 'curso_id' => null]);
        $mensaje = Message::create([
            'thread_id' => $hilo->id,
            'user_id' => $user->id,
            'body' => 'Test body',
        ]);
        Participant::create(['thread_id' => $hilo->id, 'user_id' => $user->id]);

        $mailable = new Alerta($mensaje);

        $mailable->assertHasSubject(__('Important notice'));
        $mailable->build();
    }

    public function testExportCompletado()
    {
        $mailable = new ExportCompletado('https://example.com/export.zip');

        $mailable->assertHasSubject(__('Export completed'));
        $mailable->build();
    }

    public function testFeedbackRecibido()
    {
        $tarea = Tarea::factory()->create();

        $mailable = new FeedbackRecibido($tarea);

        $mailable->assertHasSubject(__('Review completed'));
        $mailable->build();
    }

    public function testNotificationTest()
    {
        $mailable = new NotificationTest();

        $mailable->assertHasSubject(__('Notification test'));
        $mailable->build();
    }

    public function testNuevoMensaje()
    {
        $user = User::factory()->create();
        $hilo = Hilo::create(['subject' => 'Test', 'owner_id' => $user->id, 'curso_id' => null]);
        $mensaje = Message::create([
            'thread_id' => $hilo->id,
            'user_id' => $user->id,
            'body' => 'Test body',
        ]);
        Participant::create(['thread_id' => $hilo->id, 'user_id' => $user->id]);

        $mailable = new NuevoMensaje($mensaje);

        $mailable->assertHasSubject(__('New message'));
        $mailable->build();
    }

    public function testNuevoUsuario()
    {
        $user = User::factory()->create();

        $mailable = new NuevoUsuario($user);

        $mailable->assertHasSubject(__('New user registered'));
        $mailable->build();
    }

    public function testPlazoAmpliado()
    {
        $mailable = new PlazoAmpliado('John', 'Activity 1');

        $mailable->assertHasSubject(__('Deadline extended'));
        $mailable->build();
    }

    public function testRepositorioClonado()
    {
        $mailable = new RepositorioClonado();

        $mailable->assertHasSubject(__('Repository cloned'));
        $mailable->build();
    }

    public function testRepositorioClonadoError()
    {
        $mailable = new RepositorioClonadoError();

        $mailable->assertHasSubject(__('Repository cloning error'));
        $mailable->build();
    }

    public function testTareaEnviada()
    {
        $tarea = Tarea::factory()->create();

        $mailable = new TareaEnviada($tarea);

        $mailable->assertHasSubject(__('New submission received'));
        $mailable->build();
    }
}

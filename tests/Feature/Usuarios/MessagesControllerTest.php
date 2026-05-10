<?php

namespace Tests\Feature\Usuarios;

use App\Models\Curso;
use App\Models\Hilo;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Override;
use Tests\TestCase;

class MessagesControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    private function createHiloWithParticipant($user, $curso): Hilo
    {
        $hilo = Hilo::create([
            'subject' => 'Test thread',
            'owner_id' => $user->id,
            'curso_id' => $curso->id,
        ]);

        Message::create([
            'thread_id' => $hilo->id,
            'user_id' => $user->id,
            'body' => 'Test message body',
        ]);

        Participant::create([
            'thread_id' => $hilo->id,
            'user_id' => $user->id,
            'last_read' => new Carbon,
        ]);

        return $hilo;
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('messages'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAuthNotIndex()
    {
        // When
        $response = $this->get(route('messages'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        $curso->users()->attach($this->alumno);
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('messages.create'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAuthNotCreate()
    {
        // When
        $response = $this->get(route('messages.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreateTeam()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        $curso->users()->attach($this->alumno);
        setting_usuario(['curso_actual' => $curso->id]);
        $team = \App\Models\Team::factory()->create();
        $team->users()->attach($this->alumno);

        // When
        $response = $this->post(route('messages.create-with-subject-team'), [
            'team_id' => $team->id,
        ]);

        // Then
        $response->assertSuccessful();
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->alumno);
        Mail::fake();

        // Given
        $curso = Curso::factory()->create();
        $curso->users()->attach($this->alumno);
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->post(route('messages.store'), [
            'subject' => 'Test subject',
            'message' => '<p>Test message content</p>',
            'recipients' => [$this->profesor->id],
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('threads', ['subject' => 'Test subject']);
    }

    public function testNotAuthNotStore()
    {
        // When
        $response = $this->post(route('messages.store'), [
            'subject' => 'Test subject',
            'message' => 'Test',
            'recipients' => [1],
        ]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $hilo = $this->createHiloWithParticipant($this->alumno, $curso);

        // When
        $response = $this->get(route('messages.show', $hilo->id));

        // Then
        $response->assertSuccessful();
    }

    public function testShowNotFound()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When - thread ID 99999 doesn't exist
        $response = $this->get(route('messages.show', 99999));

        // Then
        $response->assertRedirect(route('messages'));
    }

    public function testNotAuthNotShow()
    {
        // When
        $response = $this->get(route('messages.show', 1));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->alumno);
        Mail::fake();

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $hilo = $this->createHiloWithParticipant($this->alumno, $curso);

        // When
        $response = $this->put(route('messages.update', $hilo->id), [
            'message' => '<p>Reply message content</p>',
        ]);

        // Then
        $response->assertRedirect(route('messages.show', $hilo->id));
    }

    public function testNotAuthNotUpdate()
    {
        // When
        $response = $this->put(route('messages.update', 1), [
            'message' => 'Test',
        ]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDestroy()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        $hilo = $this->createHiloWithParticipant($this->alumno, $curso);

        // When
        $response = $this->delete(route('messages.destroy', $hilo->id));

        // Then
        $response->assertRedirect();
        $this->assertSoftDeleted('threads', ['id' => $hilo->id]);
    }

    public function testNotAuthNotDestroy()
    {
        // When
        $response = $this->delete(route('messages.destroy', 1));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDestroyMessage()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();
        $hilo = $this->createHiloWithParticipant($this->alumno, $curso);
        $message = $hilo->messages()->first();

        // When
        $response = $this->delete(route('messages.destroy_message', $message->id));

        // Then
        $response->assertRedirect();
        $this->assertSoftDeleted('messages', ['id' => $message->id]);
    }

    public function testNotAuthNotDestroyMessage()
    {
        // When
        $response = $this->delete(route('messages.destroy_message', 1));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testAllMethod()
    {
        // The all() method has no route; call it directly
        $this->actingAs($this->alumno);
        setting_usuario(['curso_actual' => null]);

        $controller = app(\App\Http\Controllers\MessagesController::class);
        $response = $controller->all();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }
}

<?php

namespace Tests\Feature\Usuarios;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('notifications.edit'));

        // Then
        $response->assertSuccessful()->assertSee(__('Notification settings'));
    }

    public function testNotAuthNotEdit()
    {
        // When
        $response = $this->get(route('notifications.edit'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = [
            'notificacion_mensaje_recibido' => true,
            'notificacion_feedback_recibido' => true,
        ];

        // When
        $response = $this->put(route('notifications.update'), $data);

        // Then
        $response->assertSuccessful()->assertSee(__('Notification settings'));
    }

    public function testNotAuthNotUpdate()
    {
        // When
        $response = $this->put(route('notifications.update'), []);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testTest()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        \Illuminate\Support\Facades\Mail::fake();

        // When
        $response = $this->get(route('notifications.test'));

        // Then
        $response->assertRedirect(route('notifications.edit'));
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\NotificationTest::class);
    }

    public function testNotAuthNotTest()
    {
        // Auth
        // When
        $response = $this->get(route('notifications.test'));

        // Then
        $response->assertRedirect(route('login'));
    }
}

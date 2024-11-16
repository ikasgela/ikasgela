<?php

namespace Tests\Feature\Profesor;

use App\Models\Curso;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MensajeDirectoAlumnoTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    #[Test]
    public function seleccionar_alumno_al_enviar_mensaje()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $user = $this->alumno;

        $curso = Curso::factory()->create();

        setting_usuario(['curso_actual' => $curso->id], $user); // Alumno
        setting_usuario(['curso_actual' => $curso->id]);        // Profesor

        $alumnos = User::factory()->count(3)
            ->create()
            ->each(function ($user) {
                $user->roles()->attach(Role::factory()->create([
                    'name' => 'alumno'
                ]));
            });

        $curso->users()->attach($this->profesor);
        $curso->users()->attach($user);
        $curso->users()->attach($alumnos);

        // When
        $response = $this->post(route('messages.create-with-subject'), [
            'user_id' => $user->id,
        ]);

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Create new conversation'),
            __('Recipients'),
            'label class',
            'input',
            'recipients[]',
            $user->id,
            'checked',
            $user->name,
            '/label',
        ]);
    }
}

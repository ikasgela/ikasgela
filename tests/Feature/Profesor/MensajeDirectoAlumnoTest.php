<?php

namespace Tests\Feature\Profesor;

use App\Curso;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MensajeDirectoAlumnoTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function seleccionar_alumno_al_enviar_mensaje()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $user = $this->alumno;

        $curso = factory(Curso::class)->create();

        setting_usuario(['curso_actual' => $curso->id], $user); // Alumno
        setting_usuario(['curso_actual' => $curso->id]);        // Profesor

        $alumnos = factory(User::class, 3)
            ->create()
            ->each(function ($user) {
                $user->roles()->attach(factory(Role::class)->create([
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
        $response->assertSeeInOrder([
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

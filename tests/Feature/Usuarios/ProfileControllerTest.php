<?php

namespace Tests\Feature\Usuarios;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('profile.show'));

        // Then
        $response->assertSuccessful()->assertSee(__('Profile'));
    }

    public function testNotAuthNotShow()
    {
        // When
        $response = $this->get(route('profile.show'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testPassword()
    {
        // Auth
        $this->actingAs($this->alumno);

        // When
        $response = $this->get(route('profile.password'));

        // Then
        $response->assertSuccessful()->assertSee(__('Password'));
    }

    public function testNotAuthNotPassword()
    {
        // When
        $response = $this->get(route('profile.password'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUser()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = [
            'name' => 'NewName',
            'surname' => 'NewSurname',
            'gravatar_email' => null,
        ];

        // When
        $response = $this->put(route('profile.update.user'), $data);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->alumno->id, 'name' => 'NewName', 'surname' => 'NewSurname']);
    }

    public function testUpdateUserRequiresName()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = ['name' => null, 'surname' => 'NewSurname'];

        // When
        $response = $this->put(route('profile.update.user'), $data);

        // Then
        $response->assertSessionHasErrors('name');
    }

    public function testUpdateUserRequiresSurname()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = ['name' => 'NewName', 'surname' => null];

        // When
        $response = $this->put(route('profile.update.user'), $data);

        // Then
        $response->assertSessionHasErrors('surname');
    }

    public function testNotAuthNotUpdateUser()
    {
        // When
        $response = $this->put(route('profile.update.user'), ['name' => 'Test', 'surname' => 'User']);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdatePassword()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = [
            'current' => 'secret',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        // When
        $response = $this->put(route('profile.update.password'), $data);

        // Then
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function testUpdatePasswordFailsWrongCurrent()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = [
            'current' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        // When
        $response = $this->put(route('profile.update.password'), $data);

        // Then
        $response->assertSessionHasErrors();
    }

    public function testUpdatePasswordRequiresCurrent()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $data = [
            'current' => null,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        // When
        $response = $this->put(route('profile.update.password'), $data);

        // Then
        $response->assertSessionHasErrors('current');
    }

    public function testNotAuthNotUpdatePassword()
    {
        // When
        $response = $this->put(route('profile.update.password'), []);

        // Then
        $response->assertRedirect(route('login'));
    }
}

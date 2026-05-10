<?php

namespace Tests\Feature\Usuarios;

use Override;
use App\Jobs\BorrarUsuario;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UsersExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testToggleHelp()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $initial = $this->alumno->tutorial;

        // When
        $response = $this->post(route('users.toggle_help'));

        // Then
        $response->assertRedirect();
        $this->alumno->refresh();
        $this->assertNotSame((bool)$initial, (bool)$this->alumno->tutorial);
    }

    public function testNotAuthNotToggleHelp()
    {
        // Auth (no user)
        // When
        $response = $this->post(route('users.toggle_help'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testPassword()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->get(route('users.password', $user));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Change password')]);
    }

    public function testNotAuthNotPassword()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->get(route('users.password', $user));

        // Then
        $response->assertForbidden();
    }

    public function testUpdatePassword()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->put(route('users.update.password', $user), [
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ]);

        // Then
        $response->assertRedirect();
    }

    public function testNotAuthNotUpdatePassword()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->put(route('users.update.password', $user), [
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testLimpiarCache()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->post(route('users.limpiar_cache', $user));

        // Then
        $response->assertRedirect();
    }

    public function testNotAuthNotLimpiarCache()
    {
        // Auth
        $this->actingAs($this->not_profesor_tutor);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->post(route('users.limpiar_cache', $user));

        // Then
        $response->assertForbidden();
    }

    public function testManualActivation()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create(['email_verified_at' => null]);

        // When
        $response = $this->post(route('users.manual_activation'), [
            'user_id' => $user->id,
        ]);

        // Then
        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function testNotAuthNotManualActivation()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->post(route('users.manual_activation'), [
            'user_id' => $this->alumno->id,
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testToggleBlocked()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create(['blocked_date' => null]);

        // When
        $response = $this->post(route('users.toggle_blocked'), [
            'user_id' => $user->id,
        ]);

        // Then
        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->blocked_date);
    }

    public function testNotAuthNotToggleBlocked()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->post(route('users.toggle_blocked'), [
            'user_id' => $user->id,
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testAccionesGrupoVerify()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user1 = User::factory()->create(['email_verified_at' => null]);
        $user2 = User::factory()->create(['email_verified_at' => null]);

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'verify',
            'usuarios_seleccionados' => [$user1->id, $user2->id],
        ]);

        // Then
        $response->assertRedirect();
        $user1->refresh();
        $this->assertNotNull($user1->email_verified_at);
    }

    public function testAccionesGrupoTag()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'tag',
            'usuarios_seleccionados' => [$user->id],
            'tags' => 'test-tag',
        ]);

        // Then
        $response->assertRedirect();
    }

    public function testAccionesGrupoEnroll()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'enroll',
            'usuarios_seleccionados' => [$user->id],
            'curso_id' => $curso->id,
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('curso_user', [
            'user_id' => $user->id,
            'curso_id' => $curso->id,
        ]);
    }

    public function testNotAuthNotAccionesGrupo()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'verify',
            'usuarios_seleccionados' => [$this->alumno->id],
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testAccionesGrupoBlock()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create(['blocked_date' => null]);

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'block',
            'usuarios_seleccionados' => [$user->id],
        ]);

        // Then
        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->blocked_date);
    }

    public function testAccionesGrupoUnblock()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create(['blocked_date' => now()]);

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'unblock',
            'usuarios_seleccionados' => [$user->id],
        ]);

        // Then
        $response->assertRedirect();
        $user->refresh();
        $this->assertNull($user->blocked_date);
    }

    public function testAccionesGrupoDelete()
    {
        // Auth
        $this->actingAs($this->admin);
        Queue::fake();

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->post(route('users.acciones_grupo'), [
            'action' => 'delete',
            'usuarios_seleccionados' => [$user->id],
        ]);

        // Then
        $response->assertRedirect();
        Queue::assertPushed(BorrarUsuario::class);
    }
}

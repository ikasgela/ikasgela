<?php

namespace Tests\Feature\Usuarios;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tests\TestCase;

class UsersCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'email', 'roles_seleccionados'
    ];

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->get(route('users.index'));

        // Then
        $response->assertSuccessful()->assertSee($user->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('users.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('users.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('users.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New user'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('users.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('users.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        $rol_alumno = Role::create(['name' => 'alumno', 'description' => 'Alumno']);

        // Given
        $user = User::factory()->make();
        $total = User::all()->count();

        // When
        $this->post(route('users.store'), array_merge($user->toArray(), [
                'roles_seleccionados' => [$rol_alumno->id],
                'password' => 'dummy',
            ]
        ));

        // Then
        $this->assertEquals($total + 1, User::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->make();

        // When
        $response = $this->post(route('users.store'), $user->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $user = User::factory()->make();

        // When
        $response = $this->post(route('users.store'), $user->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = User::all()->count();

        $empty = new User();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('users.store'), array_merge($empty->toArray(), [
                'password' => 'dummy',
            ]
        ));

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->make([$field => null]);

        // When
        $response = $this->post(route('users.store'), $user->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testNotShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException(RouteNotFoundException::class);

        // When
        $this->get(route('users.show'));

        // Then
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->get(route('users.edit', $user), $user->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$user->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->get(route('users.edit', $user), $user->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $user = User::factory()->create();

        // When
        $response = $this->get(route('users.edit', $user), $user->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();
        $user->name = "Updated";

        $rol_admin = Role::create(['name' => 'admin', 'description' => 'Administrador']);
        $rol_alumno = Role::create(['name' => 'alumno', 'description' => 'Alumno']);
        $rol_profesor = Role::create(['name' => 'profesor', 'description' => 'Profesor']);
        $rol_tutor = Role::create(['name' => 'tutor', 'description' => 'Tutor']);

        // When
        $this->put(route('users.update', $user), array_merge($user->toArray(), [
                'roles_seleccionados' => [$rol_alumno->id]
            ]
        ));

        // Then
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name]);

        $this->assertDatabaseHas('role_user', ['user_id' => $user->id, 'role_id' => $rol_alumno->id]);

        $this->assertDatabaseMissing('role_user', ['user_id' => $user->id, 'role_id' => $rol_admin->id]);
        $this->assertDatabaseMissing('role_user', ['user_id' => $user->id, 'role_id' => $rol_profesor->id]);
        $this->assertDatabaseMissing('role_user', ['user_id' => $user->id, 'role_id' => $rol_tutor->id]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->create();
        $user->name = "Updated";

        // When
        $response = $this->put(route('users.update', $user), $user->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $user = User::factory()->create();
        $user->name = "Updated";

        // When
        $response = $this->put(route('users.update', $user), $user->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();
        $empty = new User();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('users.update', $user), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();
        $user->$field = null;

        // When
        $response = $this->put(route('users.update', $user), $user->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->updateRequires($field);
        }
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = User::factory()->create();

        // When
        $response = $this->delete(route('users.destroy', $user));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $user = User::factory()->create();

        // When
        $response = $this->delete(route('users.destroy', $user));

        // Then
        $response->assertRedirect(route('login'));
    }
}

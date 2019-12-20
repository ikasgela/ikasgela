<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $response = $this->get(route('users.index'));

        // Then
        $response->assertSee($user->name);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('users.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }

    /*
        public function testCreate()
        {
            // Given
            $this->actingAs($this->admin);

            // When
            $response = $this->get(route('users.create'));

            // Then
            $response->assertSeeInOrder([__('New user'), __('Save')]);
        }

        public function testNotAdminNotCreate()
        {
            // Given
            $this->actingAs($this->not_admin);

            // When
            // Then
            $this->get(route('users.create'))
                ->assertForbidden();
        }

        public function testNotAuthNotCreate()
        {
            // Given
            // When
            // Then
            $this->get(route('users.create'))
                ->assertRedirect(route('login'));
        }

        public function testStore()
        {
            // Given
            $this->actingAs($this->admin);
            $user = factory(User::class)->make();

            // When
            $this->post(route('users.store'), $user->toArray());

            // Then
            $this->assertEquals(1, User::all()->count());
        }

        public function testNotAdminNotStore()
        {
            // Given
            $this->actingAs($this->not_admin);
            $user = factory(User::class)->make();

            // When
            // Then
            $this->post(route('users.store'), $user->toArray())
                ->assertForbidden();
        }

        public function testNotAuthNotStore()
        {
            // Given
            $user = factory(User::class)->make();

            // When
            // Then
            $this->post(route('users.store'), $user->toArray())
                ->assertRedirect(route('login'));
        }

        public function testStoreRequiresName()
        {
            // Given
            $this->actingAs($this->admin);
            $user = factory(User::class)->make(['name' => null]);

            // When
            // Then
            $this->post(route('users.store'), $user->toArray())
                ->assertSessionHasErrors('name');
        }

        public function testStoreRequiresGroup()
        {
            // Given
            $this->actingAs($this->admin);
            $user = factory(User::class)->make(['group_id' => null]);

            // When
            // Then
            $this->post(route('users.store'), $user->toArray())
                ->assertSessionHasErrors('group_id');
        }

        public function testShow()
        {
            // Given
            $this->actingAs($this->admin);
            $user = factory(User::class)->create();

            // When
            $response = $this->get(route('users.show', $user));

            // Then
            $response->assertSee(__('Not implemented.'));
        }

        public function testNotAdminNotShow()
        {
            // Given
            $this->actingAs($this->not_admin);
            $user = factory(User::class)->create();

            // When
            // Then
            $this->get(route('users.show', $user))
                ->assertForbidden();
        }

        public function testNotAuthNotShow()
        {
            // Given
            $user = factory(User::class)->create();

            // When
            // Then
            $this->get(route('users.show', $user))
                ->assertRedirect(route('login'));
        }
    */
    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $response = $this->get(route('users.edit', $user), $user->toArray());

        // Then
        $response->assertSeeInOrder([$user->name, $user->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $user = factory(User::class)->create();

        // When
        // Then
        $this->get(route('users.edit', $user), $user->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $user = factory(User::class)->create();

        // When
        // Then
        $this->get(route('users.edit', $user), $user->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();
        $user->name = "Updated";

        $rol_admin = Role::where('name', 'admin')->first();
        $rol_profesor = Role::where('name', 'profesor')->first();
        $rol_alumno = Role::where('name', 'alumno')->first();

        // When
        $this->put(route('users.update', [
            'user' => $user->id
        ]), array_merge(
            $user->toArray(),
            ['roles_seleccionados' => [$rol_alumno->id]]
        ));

        // Then
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name]);
        $this->assertDatabaseHas('role_user', ['user_id' => $user->id, 'role_id' => $rol_alumno->id]);
        $this->assertDatabaseMissing('role_user', ['user_id' => $user->id, 'role_id' => $rol_admin->id]);
        $this->assertDatabaseMissing('role_user', ['user_id' => $user->id, 'role_id' => $rol_profesor->id]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $user = factory(User::class)->create();
        $user->name = "Updated";

        // When
        // Then
        $this->put(route('users.update', $user), $user->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $user = factory(User::class)->create();
        $user->name = "Updated";

        // When
        // Then
        $this->put(route('users.update', $user), $user->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresName()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $user->name = null;

        // Then
        $this->put(route('users.update', $user), $user->toArray())
            ->assertSessionHasErrors('name');
    }

    public function testUpdateRequiresEmail()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $user->email = null;

        // Then
        $this->put(route('users.update', $user), $user->toArray())
            ->assertSessionHasErrors('email');
    }

    public function testUpdateRequiresUsername()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $user->username = null;

        // Then
        $this->put(route('users.update', $user), $user->toArray())
            ->assertSessionHasErrors('username');
    }

    public function testUpdateRequiresRolesSeleccionados()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        // Then
        $this->put(route('users.update', $user), $user->toArray())
            ->assertSessionHasErrors('roles_seleccionados');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $this->delete(route('users.destroy', $user));

        // Then
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $user = factory(User::class)->create();

        // When
        // Then
        $this->delete(route('users.destroy', $user))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $user = factory(User::class)->create();

        // When
        // Then
        $this->delete(route('users.destroy', $user))
            ->assertRedirect(route('login'));
    }
}

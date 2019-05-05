<?php

namespace Tests\Feature;

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
            $response = $this->get(route('users.show', ['id' => $user->id]));

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
            $this->get(route('users.show', ['id' => $user->id]))
                ->assertForbidden();
        }

        public function testNotAuthNotShow()
        {
            // Given
            $user = factory(User::class)->create();

            // When
            // Then
            $this->get(route('users.show', ['id' => $user->id]))
                ->assertRedirect(route('login'));
        }
    */
    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $response = $this->get(route('users.edit', ['id' => $user->id]), $user->toArray());

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
        $this->get(route('users.edit', ['id' => $user->id]), $user->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $user = factory(User::class)->create();

        // When
        // Then
        $this->get(route('users.edit', ['id' => $user->id]), $user->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();
        $user->name = "Updated";

        // When
        $this->put(route('users.update', ['id' => $user->id]), $user->toArray());

        // Then
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $user = factory(User::class)->create();
        $user->name = "Updated";

        // When
        // Then
        $this->put(route('users.update', ['id' => $user->id]), $user->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $user = factory(User::class)->create();
        $user->name = "Updated";

        // When
        // Then
        $this->put(route('users.update', ['id' => $user->id]), $user->toArray())
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
        $this->put(route('users.update', ['id' => $user->id]), $user->toArray())
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
        $this->put(route('users.update', ['id' => $user->id]), $user->toArray())
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
        $this->put(route('users.update', ['id' => $user->id]), $user->toArray())
            ->assertSessionHasErrors('username');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $user = factory(User::class)->create();

        // When
        $this->delete(route('users.destroy', ['id' => $user->id]));

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
        $this->delete(route('users.destroy', ['id' => $user->id]))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $user = factory(User::class)->create();

        // When
        // Then
        $this->delete(route('users.destroy', ['id' => $user->id]))
            ->assertRedirect(route('login'));
    }
}

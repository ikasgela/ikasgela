<?php

namespace Tests\Feature\Usuarios;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UsersCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'email', 'username', 'roles_seleccionados'
    ];

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
        $user = factory(User::class)->create();

        // When
        $response = $this->get(route('users.index'));

        // Then
        $response->assertSee($user->name);
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

    public function testNotCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException('Symfony\Component\Routing\Exception\RouteNotFoundException');

        // When
        $this->get(route('users.create'));

        // Then
    }

    public function testNotStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException('Symfony\Component\Routing\Exception\RouteNotFoundException');

        // When
        $this->post(route('users.store'));

        // Then
    }

    public function testNotShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $this->expectException('Symfony\Component\Routing\Exception\RouteNotFoundException');

        // When
        $this->get(route('users.show'));

        // Then
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = factory(User::class)->create();

        // When
        $response = $this->get(route('users.edit', $user), $user->toArray());

        // Then
        $response->assertSeeInOrder([$user->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = factory(User::class)->create();

        // When
        $response = $this->get(route('users.edit', $user), $user->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $user = factory(User::class)->create();

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
        $user = factory(User::class)->create();
        $user->name = "Updated";

        // When
        $this->put(route('users.update', $user), $user->toArray());

        // Then
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = factory(User::class)->create();
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
        $user = factory(User::class)->create();
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
        $user = factory(User::class)->create();
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
        $user = factory(User::class)->create();
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

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = factory(User::class)->create();

        // When
        $this->delete(route('users.destroy', $user));

        // Then
        $this->assertDatabaseMissing('users', $user->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = factory(User::class)->create();

        // When
        $response = $this->delete(route('users.destroy', $user));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $user = factory(User::class)->create();

        // When
        $response = $this->delete(route('users.destroy', $user));

        // Then
        $response->assertRedirect(route('login'));
    }
}

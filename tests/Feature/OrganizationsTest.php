<?php

namespace Tests\Feature;

use App\Organization;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $fecha = Carbon::now();
        $this->user = factory(User::class)->create();
        $this->user->email_verified_at = $fecha;
        $rol = Role::create(['name' => 'admin', 'description' => 'Administrador']);
        $this->user->roles()->attach($rol);
    }

    public function testIndex()
    {
        // Given
        $organization = factory(Organization::class)->create();

        // When
        $response = $this
            ->actingAs($this->user)
            ->get('/organizations');

        // Then
        $response->assertSee($organization->name);
    }

    public function testCreate()
    {
        // Given
        $organization = factory(Organization::class)->make();
        $this->actingAs($this->user);

        // When
        $this->post('/organizations', $organization->toArray());

        // Then
        $this->assertEquals(1, Organization::all()->count());
    }

    public function testNotAuthNotCreate()
    {
        // Given
        $organization = factory(Organization::class)->make();

        // When
        // Then
        $this
            ->post('/organizations', $organization->toArray())
            ->assertRedirect('/login');
    }

    public function testRequiredName()
    {
        $this->actingAs($this->user);

        $organization = factory(Organization::class)->make(['name' => null]);

        $this->post('/organizations', $organization->toArray())
            ->assertSessionHasErrors('name');
    }

}

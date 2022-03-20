<?php

namespace Tests\Feature\Estructura;

use App\Models\Period;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PeriodsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'organization_id'
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
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.index'));

        // Then
        $response->assertSuccessful()->assertSee($period->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('periods.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('periods.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('periods.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New period'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('periods.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('periods.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->make();
        $total = Period::all()->count();

        // When
        $this->post(route('periods.store'), $period->toArray());

        // Then
        $this->assertEquals($total + 1, Period::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $period = Period::factory()->make();

        // When
        $response = $this->post(route('periods.store'), $period->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $period = Period::factory()->make();

        // When
        $response = $this->post(route('periods.store'), $period->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Period::all()->count();

        $empty = new Period();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('periods.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->make([$field => null]);

        // When
        $response = $this->post(route('periods.store'), $period->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field) {
            $this->storeRequires($field);
        }
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.show', $period));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.show', $period));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.show', $period));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.edit', $period), $period->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$period->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.edit', $period), $period->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->get(route('periods.edit', $period), $period->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->create();
        $period->name = "Updated";

        // When
        $this->put(route('periods.update', $period), $period->toArray());

        // Then
        $this->assertDatabaseHas('periods', ['id' => $period->id, 'name' => $period->name]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $period = Period::factory()->create();
        $period->name = "Updated";

        // When
        $response = $this->put(route('periods.update', $period), $period->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $period = Period::factory()->create();
        $period->name = "Updated";

        // When
        $response = $this->put(route('periods.update', $period), $period->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->create();
        $empty = new Period();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('periods.update', $period), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $period = Period::factory()->create();
        $period->$field = null;

        // When
        $response = $this->put(route('periods.update', $period), $period->toArray());

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
        $period = Period::factory()->create();

        // When
        $this->delete(route('periods.destroy', $period));

        // Then
        $this->assertDatabaseMissing('periods', $period->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->delete(route('periods.destroy', $period));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $period = Period::factory()->create();

        // When
        $response = $this->delete(route('periods.destroy', $period));

        // Then
        $response->assertRedirect(route('login'));
    }
}

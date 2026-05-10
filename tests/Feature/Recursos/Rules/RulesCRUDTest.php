<?php

namespace Tests\Feature\Recursos\Rules;

use App\Models\Rule;
use App\Models\RuleGroup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class RulesCRUDTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rules.anyadir', $rule_group));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New rule'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rules.anyadir', $rule_group));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rules.anyadir', $rule_group));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->make();
        $total = Rule::all()->count();

        // When
        $this->post(route('rules.store'), $rule->toArray());

        // Then
        $this->assertEquals($total + 1, Rule::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule = Rule::factory()->make();

        // When
        $response = $this->post(route('rules.store'), $rule->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $rule = Rule::factory()->make();

        // When
        $response = $this->post(route('rules.store'), $rule->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreRequiresPropiedad()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->make(['propiedad' => null]);

        // When
        $response = $this->post(route('rules.store'), $rule->toArray());

        // Then
        $response->assertSessionHasErrors('propiedad');
    }

    public function testStoreRequiresOperador()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->make(['operador' => null]);

        // When
        $response = $this->post(route('rules.store'), $rule->toArray());

        // Then
        $response->assertSessionHasErrors('operador');
    }

    public function testStoreRequiresValor()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->make(['valor' => null]);

        // When
        $response = $this->post(route('rules.store'), $rule->toArray());

        // Then
        $response->assertSessionHasErrors('valor');
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->get(route('rules.edit', $rule));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Edit rule'), __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->get(route('rules.edit', $rule));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->get(route('rules.edit', $rule));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->create(['propiedad' => 'puntuacion']);
        $rule->propiedad = 'intentos';

        // When
        $this->put(route('rules.update', $rule), $rule->toArray());

        // Then
        $this->assertDatabaseHas('rules', ['id' => $rule->id, 'propiedad' => 'intentos']);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->put(route('rules.update', $rule), $rule->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->put(route('rules.update', $rule), $rule->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $this->delete(route('rules.destroy', $rule));

        // Then
        $this->assertDatabaseMissing('rules', ['id' => $rule->id]);
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->delete(route('rules.destroy', $rule));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->delete(route('rules.destroy', $rule));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->create();
        $total = Rule::all()->count();

        // When
        $this->post(route('rules.duplicar', $rule));

        // Then
        $this->assertEquals($total + 1, Rule::all()->count());
    }

    public function testNotAdminProfesorNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->post(route('rules.duplicar', $rule));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->post(route('rules.duplicar', $rule));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('rules.index'));

        // Then
        $response->assertNotFound();
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule = Rule::factory()->create();

        // When
        $response = $this->get(route('rules.show', $rule));

        // Then
        $response->assertNotFound();
    }
}

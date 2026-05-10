<?php

namespace Tests\Feature\Recursos\RuleGroups;

use App\Models\RuleGroup;
use App\Models\Selector;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class RuleGroupsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'operador',
        'accion',
        'resultado',
    ];

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
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('rule_groups.anyadir', $selector));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New rule group'), __('Save')]);
    }

    public function testNotAdminProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('rule_groups.anyadir', $selector));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        $selector = Selector::factory()->create();

        // When
        $response = $this->get(route('rule_groups.anyadir', $selector));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->make();
        $total = RuleGroup::all()->count();

        // When
        $this->post(route('rule_groups.store'), $rule_group->toArray());

        // Then
        $this->assertEquals($total + 1, RuleGroup::all()->count());
    }

    public function testNotAdminProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule_group = RuleGroup::factory()->make();

        // When
        $response = $this->post(route('rule_groups.store'), $rule_group->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $rule_group = RuleGroup::factory()->make();

        // When
        $response = $this->post(route('rule_groups.store'), $rule_group->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreRequiresOperador()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->make(['operador' => null]);

        // When
        $response = $this->post(route('rule_groups.store'), $rule_group->toArray());

        // Then
        $response->assertSessionHasErrors('operador');
    }

    public function testStoreRequiresAccion()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->make(['accion' => null]);

        // When
        $response = $this->post(route('rule_groups.store'), $rule_group->toArray());

        // Then
        $response->assertSessionHasErrors('accion');
    }

    public function testStoreRequiresResultado()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->make(['resultado' => null]);

        // When
        $response = $this->post(route('rule_groups.store'), $rule_group->toArray());

        // Then
        $response->assertSessionHasErrors('resultado');
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rule_groups.edit', $rule_group));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Edit rule group'), __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rule_groups.edit', $rule_group));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rule_groups.edit', $rule_group));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->create(['operador' => 'and']);
        $rule_group->operador = 'or';

        // When
        $this->put(route('rule_groups.update', $rule_group), $rule_group->toArray());

        // Then
        $this->assertDatabaseHas('rule_groups', ['id' => $rule_group->id, 'operador' => 'or']);
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->put(route('rule_groups.update', $rule_group), $rule_group->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->put(route('rule_groups.update', $rule_group), $rule_group->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $this->delete(route('rule_groups.destroy', $rule_group));

        // Then
        $this->assertDatabaseMissing('rule_groups', ['id' => $rule_group->id]);
    }

    public function testNotAdminProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->delete(route('rule_groups.destroy', $rule_group));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->delete(route('rule_groups.destroy', $rule_group));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();
        $total = RuleGroup::all()->count();

        // When
        $this->post(route('rule_groups.duplicar', $rule_group));

        // Then
        $this->assertEquals($total + 1, RuleGroup::all()->count());
    }

    public function testNotAdminProfesorNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->post(route('rule_groups.duplicar', $rule_group));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->post(route('rule_groups.duplicar', $rule_group));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('rule_groups.index'));

        // Then
        $response->assertNotFound();
    }

    public function testShow()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $rule_group = RuleGroup::factory()->create();

        // When
        $response = $this->get(route('rule_groups.show', $rule_group));

        // Then
        $response->assertNotFound();
    }
}

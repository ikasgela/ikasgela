<?php

namespace Tests\Feature\Evaluacion;

use App\Qualification;
use App\Skill;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QualificationsCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'name', 'curso_id'
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
        $qualification = factory(Qualification::class)->create();
        session(['filtrar_curso_actual' => $qualification->curso_id]);

        // When
        $response = $this->get(route('qualifications.index'));

        // Then
        $response->assertSee($qualification->name);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('qualifications.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('qualifications.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('qualifications.create'));

        // Then
        $response->assertSeeInOrder([__('New qualification'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('qualifications.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('qualifications.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->make();
        $total = Qualification::all()->count();

        // When
        $this->post(route('qualifications.store'), $qualification->toArray());

        // Then
        $this->assertEquals($total + 1, Qualification::all()->count());
    }

    public function testStoreWithSkills()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->make();
        $skill1 = factory(Skill::class)->create();
        $skill2 = factory(Skill::class)->create();

        $extra = [
            'skills_seleccionados' => [
                $skill1->id, $skill2->id
            ],
            'percentage_' . $skill1->id => 100,
            'percentage_' . $skill2->id => 50,
        ];

        // When
        $this->post(route('qualifications.store'), array_merge($qualification->toArray(), $extra));

        // Then
        $guardado = Qualification::orderBy('id', 'desc')->first();
        $this->assertCount(2, $guardado->skills);
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $qualification = factory(Qualification::class)->make();

        // When
        $response = $this->post(route('qualifications.store'), $qualification->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $qualification = factory(Qualification::class)->make();

        // When
        $response = $this->post(route('qualifications.store'), $qualification->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Qualification::all()->count();

        $empty = new Qualification();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('qualifications.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->make([$field => null]);

        // When
        $response = $this->post(route('qualifications.store'), $qualification->toArray());

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
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.show', $qualification));

        // Then
        $response->assertStatus(501);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.show', $qualification));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.show', $qualification));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.edit', $qualification), $qualification->toArray());

        // Then
        $response->assertSeeInOrder([$qualification->name, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.edit', $qualification), $qualification->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->get(route('qualifications.edit', $qualification), $qualification->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";

        // When
        $this->put(route('qualifications.update', $qualification), $qualification->toArray());

        // Then
        $this->assertDatabaseHas('qualifications', ['id' => $qualification->id, 'name' => $qualification->name]);
    }

    public function testUpdateWithSkills()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";
        $skill1 = factory(Skill::class)->create();
        $skill2 = factory(Skill::class)->create();

        $extra = [
            'skills_seleccionados' => [
                $skill1->id, $skill2->id
            ],
            'percentage_' . $skill1->id => 100,
            'percentage_' . $skill2->id => 50,
        ];

        // When
        $this->put(route('qualifications.update', $qualification), array_merge($qualification->toArray(), $extra));

        // Then
        $this->assertCount(2, $qualification->skills);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";

        // When
        $response = $this->put(route('qualifications.update', $qualification), $qualification->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $qualification = factory(Qualification::class)->create();
        $qualification->name = "Updated";

        // When
        $response = $this->put(route('qualifications.update', $qualification), $qualification->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->create();
        $empty = new Qualification();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('qualifications.update', $qualification), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $qualification = factory(Qualification::class)->create();
        $qualification->$field = null;

        // When
        $response = $this->put(route('qualifications.update', $qualification), $qualification->toArray());

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
        $qualification = factory(Qualification::class)->create();

        // When
        $this->delete(route('qualifications.destroy', $qualification));

        // Then
        $this->assertDatabaseMissing('qualifications', $qualification->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->delete(route('qualifications.destroy', $qualification));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $qualification = factory(Qualification::class)->create();

        // When
        $response = $this->delete(route('qualifications.destroy', $qualification));

        // Then
        $response->assertRedirect(route('login'));
    }
}

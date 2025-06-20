<?php

namespace Tests\Feature\Alumno;

use Override;
use App\Models\Actividad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArchivoTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'texto', 'pregunta_id'
    ];

    #[Override]
    public function setUp(): void
    {
        //$this->markTestSkipped('Tests desactivados.');

        parent::setUp();
        parent::crearUsuarios();
    }

    #[Test]
    public function any_non_admin_user_can_see_own_archived_activities()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = $this->not_admin;

        $actividad1 = Actividad::factory()->create();
        $actividad2 = Actividad::factory()->create();

        $actividad2->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad2->unidad->save();
        $actividad3 = Actividad::factory()->create();
        $actividad3->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad3->unidad->save();

        setting_usuario(['curso_actual' => $actividad1->unidad->curso->id]);

        $user->actividades()->attach($actividad1, ['estado' => 60]);
        $user->actividades()->attach($actividad2);
        $user->actividades()->attach($actividad3, ['estado' => 60]);

        // When
        $response = $this->get(route('archivo.index'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Archived'),
            $actividad1->nombre,
            $actividad3->nombre,
        ]);
    }

    #[Test]
    public function profesor_can_see_others_archive()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $user = $this->not_admin;

        $actividad1 = Actividad::factory()->create();
        $actividad2 = Actividad::factory()->create();
        $actividad2->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad2->unidad->save();
        $actividad3 = Actividad::factory()->create();
        $actividad3->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad3->unidad->save();

        setting_usuario(['curso_actual' => $actividad1->unidad->curso->id]);

        $user->actividades()->attach($actividad1, ['estado' => 60]);
        $user->actividades()->attach($actividad2);
        $user->actividades()->attach($actividad3, ['estado' => 60]);

        // When
        $response = $this->post(route('archivo.index'), ['user_id' => $user->id]);

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Archived'),
            $actividad1->nombre,
            $actividad3->nombre,
        ]);
    }

    #[Test]
    public function tutor_can_see_others_archive()
    {
        // Auth
        $this->actingAs($this->tutor);

        // Given
        $user = $this->not_admin;

        $actividad1 = Actividad::factory()->create();
        $actividad2 = Actividad::factory()->create();
        $actividad2->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad2->unidad->save();
        $actividad3 = Actividad::factory()->create();
        $actividad3->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad3->unidad->save();

        setting_usuario(['curso_actual' => $actividad1->unidad->curso->id]);

        $user->actividades()->attach($actividad1, ['estado' => 60]);
        $user->actividades()->attach($actividad2);
        $user->actividades()->attach($actividad3, ['estado' => 60]);

        // When
        $response = $this->post(route('archivo.index'), ['user_id' => $user->id]);

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Archived'),
            $actividad1->nombre,
            $actividad3->nombre,
        ]);
    }

    #[Test]
    public function only_profesor_tutor_can_see_others_archive()
    {
        // Auth
        $this->actingAs($this->not_profesor_tutor);

        // Given
        $user = $this->not_admin;

        $actividad1 = Actividad::factory()->create();
        $actividad2 = Actividad::factory()->create();
        $actividad2->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad2->unidad->save();
        $actividad3 = Actividad::factory()->create();
        $actividad3->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad3->unidad->save();

        setting_usuario(['curso_actual' => $actividad1->unidad->curso->id]);

        $user->actividades()->attach($actividad1, ['estado' => 60]);
        $user->actividades()->attach($actividad2);
        $user->actividades()->attach($actividad3, ['estado' => 60]);

        // When
        $response = $this->post(route('archivo.index'), ['user_id' => $user->id]);

        // Then
        $response->assertForbidden();
    }

    #[Test]
    public function any_non_admin_user_can_see_own_archived_activity_detail()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $user = $this->not_admin;

        $actividad1 = Actividad::factory()->create();
        $actividad2 = Actividad::factory()->create();
        $actividad2->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad2->unidad->save();
        $actividad3 = Actividad::factory()->create();
        $actividad3->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad3->unidad->save();

        setting_usuario(['curso_actual' => $actividad1->unidad->curso->id]);

        $user->actividades()->attach($actividad1, ['estado' => 60]);
        $user->actividades()->attach($actividad2);
        $user->actividades()->attach($actividad3, ['estado' => 60]);

        // When
        $response = $this->get(route('archivo.show', $actividad1));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([
            __('Archived'),
            $actividad1->nombre,
        ]);
    }

    #[Test]
    public function only_owner_can_see_own_archived_activity_detail()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $user = $this->not_admin;

        $actividad1 = Actividad::factory()->create();
        $actividad2 = Actividad::factory()->create();
        $actividad2->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad2->unidad->save();
        $actividad3 = Actividad::factory()->create();
        $actividad3->unidad->curso_id = $actividad1->unidad->curso->id;
        $actividad3->unidad->save();

        setting_usuario(['curso_actual' => $actividad1->unidad->curso->id]);

        $user->actividades()->attach($actividad1, ['estado' => 60]);
        $user->actividades()->attach($actividad2);
        $user->actividades()->attach($actividad3, ['estado' => 60]);

        // When
        $response = $this->get(route('archivo.show', $actividad1));

        // Then
        $response->assertNotFound();
    }
}

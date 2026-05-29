<?php

namespace Tests\Feature\Recursos\Cuestionarios;

use Override;
use App\Http\Middleware\UserLocale;
use App\Models\Cuestionario;
use App\Models\Curso;
use App\Models\Pregunta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Once;
use Tests\TestCase;

class PreguntasCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'titulo', 'texto', 'cuestionario_id'
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
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('preguntas.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertStatus(404);
    }

    public function testNotProfesorNotCreate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('preguntas.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->make();
        $total = Pregunta::all()->count();

        // When
        $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $this->assertCount($total + 1, Pregunta::all());
    }

    public function testStoreThenEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->make();

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $guardado = Pregunta::orderBy('id', 'desc')->first();
        $response->assertLocation(route('preguntas.edit', $guardado));
    }

    public function testNotProfesorNotStore()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->make();

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->make();

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $total = Pregunta::all()->count();

        $empty = new Pregunta();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('preguntas.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->make([$field => null]);

        // When
        $response = $this->post(route('preguntas.store'), $pregunta->toArray());

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
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertStatus(404);
    }

    public function testNotProfesorNotShow()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.show', $pregunta));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);

        // Given
        $pregunta = $this->createPreguntaInCurso();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$pregunta->texto, __('Save')]);
    }

    public function testNotProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->get(route('preguntas.edit', $pregunta), $pregunta->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);

        // Given
        $pregunta = $this->createPreguntaInCurso();
        $pregunta->texto = "Updated";

        // When
        $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $this->assertDatabaseHas('preguntas', ['id' => $pregunta->id, 'texto' => $pregunta->texto]);
    }

    public function testNotProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();
        $pregunta->texto = "Updated";

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();
        $pregunta->texto = "Updated";

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);

        // Given
        $pregunta = $this->createPreguntaInCurso();
        $empty = new Pregunta();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);
        Once::flush();

        // Given
        $pregunta = $this->createPreguntaInCurso();
        $pregunta->$field = null;

        // When
        $response = $this->put(route('preguntas.update', $pregunta), $pregunta->toArray());

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
        $this->actingAs($this->profesor);
        $this->withoutMiddleware(UserLocale::class);

        // Given
        $pregunta = $this->createPreguntaInCurso();

        // When
        $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $this->assertDatabaseMissing('preguntas', $pregunta->toArray());
    }

    public function testNotProfesorNotDelete()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->delete(route('preguntas.destroy', $pregunta));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $pregunta = Pregunta::factory()->create();
        $count = Pregunta::count();

        // When
        $this->post(route('preguntas.duplicar', $pregunta));

        // Then
        $this->assertSame($count + 1, Pregunta::count());
    }

    public function testNotProfesorNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->post(route('preguntas.duplicar', $pregunta));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Auth
        // Given
        $pregunta = Pregunta::factory()->create();

        // When
        $response = $this->post(route('preguntas.duplicar', $pregunta));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testReordenar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $a1 = Pregunta::factory()->create();
        $a2 = Pregunta::factory()->create();
        $orden1 = $a1->orden;
        $orden2 = $a2->orden;

        // When
        $this->post(route('preguntas.reordenar', [$a1, $a2]));

        // Then
        $this->assertDatabaseHas('preguntas', ['id' => $a1->id, 'orden' => $orden2]);
        $this->assertDatabaseHas('preguntas', ['id' => $a2->id, 'orden' => $orden1]);
    }

    public function testNotProfesorNotReordenar()
    {
        // Auth
        $this->actingAs($this->not_profesor);

        // Given
        $a1 = Pregunta::factory()->create();
        $a2 = Pregunta::factory()->create();

        // When
        $response = $this->post(route('preguntas.reordenar', [$a1, $a2]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotReordenar()
    {
        // Auth
        // Given
        $a1 = Pregunta::factory()->create();
        $a2 = Pregunta::factory()->create();

        // When
        $response = $this->post(route('preguntas.reordenar', [$a1, $a2]));

        // Then
        $response->assertRedirect(route('login'));
    }

    private function createPreguntaInCurso(): Pregunta
    {
        $curso = Curso::factory()->create();
        Cache::tags('user_' . $this->profesor->id)->put('curso_actual', $curso->id, config('ikasgela.eloquent_cache_time'));
        $cuestionario = Cuestionario::factory()->create(['curso_id' => $curso->id]);
        return Pregunta::factory()->create(['cuestionario_id' => $cuestionario->id]);
    }
}

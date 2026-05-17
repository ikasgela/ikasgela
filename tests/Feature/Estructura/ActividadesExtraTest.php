<?php

namespace Tests\Feature\Estructura;

use Override;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Cuestionario;
use App\Models\FileResource;
use App\Models\FileUpload;
use App\Models\IntellijProject;
use App\Models\LinkCollection;
use App\Models\MarkdownText;
use App\Models\Rubric;
use App\Models\Selector;
use App\Models\TestResult;
use App\Models\YoutubeVideo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActividadesExtraTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    // --- plantillas (requires role:admin due to controller __construct middleware) ---

    public function testPlantillas()
    {
        // Auth - requires admin (controller __construct adds role:admin to all except actualizarEstado and preview)
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        setting_usuario(['curso_actual' => $actividad->unidad->curso_id]);

        // When
        $response = $this->get(route('actividades.plantillas'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAdminNotPlantillas()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->get(route('actividades.plantillas'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotPlantillas()
    {
        // When
        $response = $this->get(route('actividades.plantillas'));

        // Then
        $response->assertRedirect(route('login'));
    }

    // --- preview (exception in __construct: no role:admin, only role:alumno|profesor) ---

    public function testPreview()
    {
        // Auth - profesor can access preview (excluded from admin-only constraint)
        $this->actingAs($this->profesor);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.preview', $actividad));

        // Then
        $response->assertSuccessful();
    }

    public function testAlumnoNotPlantillaNotPreview()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given - actividad that is NOT a plantilla
        $actividad = Actividad::factory()->create(['plantilla' => false]);

        // When
        $response = $this->get(route('actividades.preview', $actividad));

        // Then
        $response->assertNotFound();
    }

    public function testNotAuthNotPreview()
    {
        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->get(route('actividades.preview', $actividad));

        // Then
        $response->assertRedirect(route('login'));
    }

    // --- reordenar (requires role:admin via controller __construct) ---

    public function testReordenar()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given - factory sets orden = id after creation, so update them explicitly
        $a1 = Actividad::factory()->create();
        $a2 = Actividad::factory()->create();
        $a1->update(['orden' => 1]);
        $a2->update(['orden' => 2]);

        // When
        $response = $this->post(route('actividades.reordenar', [$a1, $a2]));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividades', ['id' => $a1->id, 'orden' => 2]);
        $this->assertDatabaseHas('actividades', ['id' => $a2->id, 'orden' => 1]);
    }

    public function testNotAdminNotReordenar()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $a1 = Actividad::factory()->create(['orden' => 1]);
        $a2 = Actividad::factory()->create(['orden' => 2]);

        // When
        $response = $this->post(route('actividades.reordenar', [$a1, $a2]));

        // Then
        $response->assertForbidden();
    }

    // --- reordenar_recursos (requires role:admin via controller __construct) ---

    public function testReordenarRecursos()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given - create actividad with two youtube_video resources attached with pivot.orden
        $actividad = Actividad::factory()->create();
        $v1 = YoutubeVideo::factory()->create();
        $v2 = YoutubeVideo::factory()->create();
        $actividad->youtube_videos()->attach($v1, ['orden' => 1, 'titulo_visible' => true, 'descripcion_visible' => true, 'columnas' => 12]);
        $actividad->youtube_videos()->attach($v2, ['orden' => 2, 'titulo_visible' => true, 'descripcion_visible' => true, 'columnas' => 12]);

        // When
        $response = $this->post(route('actividades.reordenar_recursos', $actividad), [
            'a1' => 1,
            'a2' => 2,
        ]);

        // Then
        $response->assertRedirect();
        $actividad->refresh();
        $recursos = $actividad->recursos->keyBy('pivot.orden');
        $this->assertSame($v2->id, $recursos->get(1)->id);
        $this->assertSame($v1->id, $recursos->get(2)->id);
    }

    public function testNotAdminNotReordenarRecursos()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.reordenar_recursos', $actividad), [
            'a1' => 1,
            'a2' => 2,
        ]);

        // Then
        $response->assertForbidden();
    }

    // --- recurso_modificar_columnas (requires role:admin via controller __construct) ---

    public function testRecursoModificarColumnasSumar()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();
        $video = YoutubeVideo::factory()->create();
        $actividad->youtube_videos()->attach($video, ['orden' => 1, 'titulo_visible' => true, 'descripcion_visible' => true, 'columnas' => 6]);

        // When
        $response = $this->post(route('actividades.recurso_modificar_columnas', $actividad), [
            'recurso' => 1,
            'accion' => 'sumar',
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_youtube_video', ['youtube_video_id' => $video->id, 'columnas' => 7]);
    }

    public function testRecursoModificarColumnasRestar()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();
        $video = YoutubeVideo::factory()->create();
        $actividad->youtube_videos()->attach($video, ['orden' => 1, 'titulo_visible' => true, 'descripcion_visible' => true, 'columnas' => 6]);

        // When
        $response = $this->post(route('actividades.recurso_modificar_columnas', $actividad), [
            'recurso' => 1,
            'accion' => 'restar',
        ]);

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('actividad_youtube_video', ['youtube_video_id' => $video->id, 'columnas' => 5]);
    }

    public function testNotAdminNotRecursoModificarColumnas()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.recurso_modificar_columnas', $actividad), [
            'recurso' => 1,
            'accion' => 'sumar',
        ]);

        // Then
        $response->assertForbidden();
    }

    // --- revisar (in role:profesor|admin group, but __construct adds role:admin → requires admin) ---

    public function testRevisar()
    {
        // Auth - admin required due to controller __construct middleware
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create(['fecha_revision' => null]);

        // When
        $response = $this->post(route('actividades.revisar', $actividad));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseMissing('actividades', ['id' => $actividad->id, 'fecha_revision' => null]);
    }

    public function testNotAdminNotRevisar()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.revisar', $actividad));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotRevisar()
    {
        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.revisar', $actividad));

        // Then
        $response->assertRedirect(route('login'));
    }

    // --- duplicar ---

    public function testDuplicar()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create();
        $count = Actividad::count();

        // When
        $response = $this->post(route('actividades.duplicar', $actividad));

        // Then
        $response->assertRedirect();
        $this->assertSame($count + 1, Actividad::count());
    }

    public function testNotAdminNotDuplicar()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.duplicar', $actividad));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDuplicar()
    {
        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.duplicar', $actividad));

        // Then
        $response->assertRedirect(route('login'));
    }

    // --- duplicar_grupo (covers crear_duplicado, mover, mover_multiple private methods) ---

    public function testDuplicarGrupo()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $count = Actividad::count();

        // When
        $response = $this->post(route('actividades.duplicar_grupo'), [
            'seleccionadas' => [$actividad->id],
            'action' => 'duplicate',
        ]);

        // Then
        $response->assertRedirect();
        $this->assertSame($count + 1, Actividad::count());
    }

    public function testDuplicarGrupoMove()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $unidad_destino = $actividad->unidad;

        // When
        $response = $this->post(route('actividades.duplicar_grupo'), [
            'seleccionadas' => [$actividad->id],
            'action' => 'move',
            'unidad_id' => $unidad_destino->id,
        ]);

        // Then
        $response->assertRedirect();
    }

    public function testNotAdminNotDuplicarGrupo()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $actividad = Actividad::factory()->create();

        // When
        $response = $this->post(route('actividades.duplicar_grupo'), [
            'seleccionadas' => [$actividad->id],
            'action' => 'duplicate',
        ]);

        // Then
        $response->assertForbidden();
    }

    // --- export ---

    public function testExport()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given - set a curso_actual so the export can build its data
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('actividades.export'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAdminNotExport()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->get(route('actividades.export'));

        // Then
        $response->assertForbidden();
    }

    // --- ampliar_plazo_todas (requires role:profesor|tutor AND role:admin → use not_profesor = alumno+admin+tutor) ---

    public function testAmpliarPlazoTodas()
    {
        // Auth - needs admin (controller __construct) AND profesor|tutor (route group)
        // not_profesor = alumno+admin+tutor: has both admin and tutor
        $this->actingAs($this->not_profesor);

        // Given - empty curso (no alumnos, nothing to iterate)
        $curso = Curso::factory()->create(['silence_notifications' => true]);

        // When
        $response = $this->post(route('actividades.ampliar_todas', $curso));

        // Then
        $response->assertRedirect();
    }

    public function testNotProfesorTutorNotAmpliarPlazoTodas()
    {
        // Auth - alumno has neither prof|tutor nor admin
        $this->actingAs($this->alumno);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('actividades.ampliar_todas', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotAmpliarPlazoTodas()
    {
        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('actividades.ampliar_todas', $curso));

        // Then
        $response->assertRedirect(route('login'));
    }

    // --- duplicar_grupo with mm action (covers mover_multiple private method) ---

    public function testDuplicarGrupoMoverMultiple()
    {
        $this->actingAs($this->admin);

        // Create two actividades in same curso with explicit orden
        $a1 = Actividad::factory()->create(['plantilla' => true, 'orden' => 1]);
        $a2 = Actividad::factory()->create([
            'plantilla' => true,
            'orden' => 2,
            'unidad_id' => $a1->unidad_id,
        ]);

        // Set curso_actual to the curso of these actividades
        setting_usuario(['curso_actual' => $a1->unidad->curso_id]);

        // Use mm_{id} action to trigger mover_multiple (move a1 towards a2)
        $response = $this->post(route('actividades.duplicar_grupo'), [
            'seleccionadas' => [$a1->id],
            'action' => 'mm_' . $a2->id,
        ]);

        $response->assertRedirect();
    }

    public function testDuplicarGrupoMoverMultipleSubir()
    {
        $this->actingAs($this->admin);

        // Create two actividades in same curso with explicit orden (move a2 towards a1 = moving up)
        $a1 = Actividad::factory()->create(['plantilla' => true, 'orden' => 10]);
        $a2 = Actividad::factory()->create([
            'plantilla' => true,
            'orden' => 20,
            'unidad_id' => $a1->unidad_id,
        ]);

        setting_usuario(['curso_actual' => $a1->unidad->curso_id]);

        // Move a2 towards a1 (upward)
        $response = $this->post(route('actividades.duplicar_grupo'), [
            'seleccionadas' => [$a2->id],
            'action' => 'mm_' . $a1->id,
        ]);

        $response->assertRedirect();
    }

    public function testDuplicarGrupoMoverMultipleConUnidadFiltro()
    {
        $this->actingAs($this->admin);

        $a1 = Actividad::factory()->create(['plantilla' => true, 'orden' => 1]);
        $a2 = Actividad::factory()->create([
            'plantilla' => true,
            'orden' => 2,
            'unidad_id' => $a1->unidad_id,
        ]);

        setting_usuario(['curso_actual' => $a1->unidad->curso_id]);

        // Set session to filter by unidad_id — covers line 636 in mover_multiple
        $this->withSession(['profesor_unidad_id_disponibles' => $a1->unidad_id]);

        $response = $this->post(route('actividades.duplicar_grupo'), [
            'seleccionadas' => [$a1->id],
            'action' => 'mm_' . $a2->id,
        ]);

        $response->assertRedirect();
    }

    // --- Actividad model method tests ---

    public function testIsExpiredConAutoAvance()
    {
        $actividad = Actividad::factory()->create(['auto_avance' => true]);

        $this->assertFalse($actividad->is_expired);
    }

    public function testEnvioPermitidoConIntellijProjectSinFork()
    {
        $actividad = Actividad::factory()->create();
        $project = IntellijProject::factory()->create();
        $actividad->intellij_projects()->attach($project, [
            'fork' => null,
            'orden' => 0,
            'titulo_visible' => true,
            'descripcion_visible' => true,
            'columnas' => 1,
        ]);

        $this->assertFalse($actividad->envioPermitido());
    }

    public function testEnvioPermitidoConCuestionarioSinResponder()
    {
        $actividad = Actividad::factory()->create();
        $cuestionario = Cuestionario::factory()->create(['respondido' => false]);
        $actividad->cuestionarios()->attach($cuestionario, [
            'orden' => 0,
            'titulo_visible' => true,
            'descripcion_visible' => true,
            'columnas' => 1,
        ]);

        $this->assertFalse($actividad->envioPermitido());
    }

    public function testEnvioPermitidoConFileUploadSinArchivos()
    {
        $actividad = Actividad::factory()->create();
        $file_upload = FileUpload::factory()->create();
        $actividad->file_uploads()->attach($file_upload, [
            'orden' => 0,
            'titulo_visible' => true,
            'descripcion_visible' => true,
            'columnas' => 1,
        ]);

        $this->assertFalse($actividad->envioPermitido());
    }

    private function pivotData(array $extra = []): array
    {
        return array_merge(['orden' => 0, 'titulo_visible' => true, 'descripcion_visible' => true, 'columnas' => 1], $extra);
    }

    public function testDuplicarRecursosConsumiblesConAleatorios()
    {
        $actividad = Actividad::factory()->create();
        $project = IntellijProject::factory()->create();
        $actividad->intellij_projects()->attach($project, $this->pivotData(['incluir_siempre' => false]));

        $actividad->duplicar_recursos_consumibles();

        // Synced to one random aleatorio project
        $this->assertCount(1, $actividad->fresh()->intellij_projects);
    }

    public function testDuplicarRecursosConsumiblesConCuestionario()
    {
        $actividad = Actividad::factory()->create();
        $cuestionario = Cuestionario::factory()->create();
        $actividad->cuestionarios()->attach($cuestionario, ['orden' => 0]);

        $actividad->duplicar_recursos_consumibles();

        // Original detached, copy attached
        $this->assertCount(1, $actividad->fresh()->cuestionarios);
        $this->assertNotEquals($cuestionario->id, $actividad->fresh()->cuestionarios->first()->id);
    }

    public function testDuplicarRecursosConsumiblesConFileUpload()
    {
        $actividad = Actividad::factory()->create();
        $file_upload = FileUpload::factory()->create();
        $actividad->file_uploads()->attach($file_upload, $this->pivotData());

        $actividad->duplicar_recursos_consumibles();

        $this->assertCount(1, $actividad->fresh()->file_uploads);
        $this->assertNotEquals($file_upload->id, $actividad->fresh()->file_uploads->first()->id);
    }

    public function testDuplicarRecursosConsumiblesConRubric()
    {
        $actividad = Actividad::factory()->create();
        $rubric = Rubric::factory()->create();
        $actividad->rubrics()->attach($rubric, $this->pivotData());

        $actividad->duplicar_recursos_consumibles();

        $this->assertCount(1, $actividad->fresh()->rubrics);
        $this->assertNotEquals($rubric->id, $actividad->fresh()->rubrics->first()->id);
    }

    public function testDuplicarRecursosConsumiblesConTestResult()
    {
        $actividad = Actividad::factory()->create();
        $test_result = TestResult::factory()->create();
        $actividad->test_results()->attach($test_result, $this->pivotData());

        $actividad->duplicar_recursos_consumibles();

        $this->assertCount(1, $actividad->fresh()->test_results);
        $this->assertNotEquals($test_result->id, $actividad->fresh()->test_results->first()->id);
    }

    public function testDuplicarRecursosConCursoNull()
    {
        $actividad = Actividad::factory()->create();
        $pivot = $this->pivotData();

        $file_resource = FileResource::factory()->create();
        $file_upload = FileUpload::factory()->create();
        $youtube_video = YoutubeVideo::factory()->create();
        $markdown_text = MarkdownText::factory()->create();
        $intellij_project = IntellijProject::factory()->create();
        $link_collection = LinkCollection::factory()->create();
        $cuestionario = Cuestionario::factory()->create();
        $rubric = Rubric::factory()->create();
        $test_result = TestResult::factory()->create();
        $selector = Selector::factory()->create();

        $actividad->file_resources()->attach($file_resource, $pivot);
        $actividad->file_uploads()->attach($file_upload, $pivot);
        $actividad->youtube_videos()->attach($youtube_video, $pivot);
        $actividad->markdown_texts()->attach($markdown_text, $pivot);
        $actividad->intellij_projects()->attach($intellij_project, $this->pivotData(['incluir_siempre' => false]));
        $actividad->link_collections()->attach($link_collection, $pivot);
        $actividad->cuestionarios()->attach($cuestionario, $pivot);
        $actividad->rubrics()->attach($rubric, $pivot);
        $actividad->test_results()->attach($test_result, $pivot);
        $actividad->selectors()->attach($selector, $pivot);

        $actividad->duplicar_recursos(null);

        $fresh = $actividad->fresh();
        $this->assertCount(1, $fresh->file_resources);
        $this->assertCount(1, $fresh->file_uploads);
        $this->assertCount(1, $fresh->youtube_videos);
        $this->assertCount(1, $fresh->markdown_texts);
        $this->assertCount(1, $fresh->intellij_projects);
        $this->assertCount(1, $fresh->link_collections);
        $this->assertCount(1, $fresh->cuestionarios);
        $this->assertCount(1, $fresh->rubrics);
        $this->assertCount(1, $fresh->test_results);
        $this->assertCount(1, $fresh->selectors);
    }
}


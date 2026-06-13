<?php

namespace Tests\Feature\Mcp;

use App\Models\Actividad;
use App\Models\Category;
use App\Models\Curso;
use App\Models\Organization;
use App\Models\Period;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Mcp\Server as McpServer;
use Laravel\Mcp\Server\Testing\PendingTestResponse;
use Tests\TestCase;

class LmsServerUnidadActividadTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    protected function mcp(string $serverClass): PendingTestResponse
    {
        return new PendingTestResponse(
            \Illuminate\Container\Container::getInstance(),
            $serverClass,
        );
    }

    // ========== Unidad Tools ==========

    public function testListUnidades()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        Unidad::factory()->count(3)->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\ListUnidades::class, ['curso_id' => $curso->id]);

        $response->assertOk();
    }

    public function testListUnidadesReturnsData()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create([
            'curso_id' => $curso->id,
            'nombre' => 'Test Unidad',
            'codigo' => 'U01',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\ListUnidades::class, ['curso_id' => $curso->id]);

        $response->assertOk()->assertSee($unidad->nombre);
    }

    public function testGetUnidad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create([
            'curso_id' => $curso->id,
            'nombre' => 'Get Unidad',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\GetUnidad::class, ['id' => $unidad->id]);

        $response->assertOk()->assertSee($unidad->nombre);
    }

    public function testGetUnidadNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\GetUnidad::class, ['id' => 9999]);

        $response->assertHasErrors(['No se encontró la unidad']);
    }

    public function testCreateUnidad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\CreateUnidad::class, [
                'curso_id' => $curso->id,
                'nombre' => 'Nueva Unidad',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('unidades', ['nombre' => 'Nueva Unidad']);
    }

    public function testCreateUnidadRequiresValidCurso()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\CreateUnidad::class, [
                'curso_id' => 9999,
                'nombre' => 'Bad Unidad',
            ]);

        $response->assertHasErrors(['No se encontró el curso']);
    }

    public function testUpdateUnidad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create([
            'curso_id' => $curso->id,
            'nombre' => 'Old Unidad',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\UpdateUnidad::class, [
                'id' => $unidad->id,
                'nombre' => 'Updated Unidad',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('unidades', ['id' => $unidad->id, 'nombre' => 'Updated Unidad']);
    }

    public function testDeleteUnidad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\DeleteUnidad::class, ['id' => $unidad->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('unidades', ['id' => $unidad->id]);
    }

    public function testDeleteUnidadRequiresAdmin()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\DeleteUnidad::class, ['id' => $unidad->id]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    // ========== Actividad Tools ==========

    public function testListActividades()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);
        Actividad::factory()->count(3)->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\ListActividades::class, ['unidad_id' => $unidad->id]);

        $response->assertOk();
    }

    public function testListActividadesReturnsData()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'unidad_id' => $unidad->id,
            'nombre' => 'Test Actividad',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\ListActividades::class, ['unidad_id' => $unidad->id]);

        $response->assertOk()->assertSee($actividad->nombre);
    }

    public function testGetActividad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'unidad_id' => $unidad->id,
            'nombre' => 'Get Actividad',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\GetActividad::class, ['id' => $actividad->id]);

        $response->assertOk()->assertSee($actividad->nombre);
    }

    public function testGetActividadNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\GetActividad::class, ['id' => 9999]);

        $response->assertHasErrors(['No se encontró la actividad']);
    }

    public function testCreateActividad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\CreateActividad::class, [
                'unidad_id' => $unidad->id,
                'nombre' => 'Nueva Actividad',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('actividades', ['nombre' => 'Nueva Actividad']);
    }

    public function testCreateActividadRequiresValidUnidad()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\CreateActividad::class, [
                'unidad_id' => 9999,
                'nombre' => 'Bad Actividad',
            ]);

        $response->assertHasErrors(['No se encontró la unidad']);
    }

    public function testUpdateActividad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'unidad_id' => $unidad->id,
            'nombre' => 'Old Actividad',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\UpdateActividad::class, [
                'id' => $actividad->id,
                'nombre' => 'Updated Actividad',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('actividades', ['id' => $actividad->id, 'nombre' => 'Updated Actividad']);
    }

    public function testDeleteActividad()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\DeleteActividad::class, ['id' => $actividad->id]);

        $response->assertOk();
        // Soft delete: check it's soft deleted
        $this->assertTrue(Actividad::withTrashed()->where('id', $actividad->id)->count() === 1);
        $this->assertNotNull(Actividad::withTrashed()->where('id', $actividad->id)->first()->deleted_at);
    }

    public function testDeleteActividadRequiresAdmin()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\DeleteActividad::class, ['id' => $actividad->id]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    // ========== ListActividadesByCurso ==========

    public function testListActividadesByCurso()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);
        Actividad::factory()->count(3)->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\ListActividadesByCurso::class, ['curso_id' => $curso->id]);

        $response->assertOk();
    }

    public function testListActividadesByCursoReturnsData()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'unidad_id' => $unidad->id,
            'nombre' => 'Curso Actividad',
        ]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\ListActividadesByCurso::class, ['curso_id' => $curso->id]);

        $response->assertOk()->assertSee($actividad->nombre);
    }

    public function testListActividadesByCursoNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\ListActividadesByCurso::class, ['curso_id' => 9999]);

        $response->assertOk();
    }

    // ========== Authorization: non-admin cannot write ==========

    public function testNonAdminCannotCreateUnidad()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\CreateUnidad::class, [
                'curso_id' => $curso->id,
                'nombre' => 'Unauthorized Unidad',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotUpdateUnidad()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\UpdateUnidad::class, [
                'id' => $unidad->id,
                'nombre' => 'Hacked',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotCreateActividad()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\CreateActividad::class, [
                'unidad_id' => $unidad->id,
                'nombre' => 'Unauthorized Actividad',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotUpdateActividad()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\UpdateActividad::class, [
                'id' => $actividad->id,
                'nombre' => 'Hacked',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    // ========== Read tools accessible to all authenticated users ==========

    public function testListUnidadesAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\ListUnidades::class, ['curso_id' => $curso->id]);

        $response->assertOk();
    }

    public function testGetUnidadAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Unidad\GetUnidad::class, ['id' => $unidad->id]);

        $response->assertOk();
    }

    public function testListActividadesAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);
        Actividad::factory()->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\ListActividades::class, ['unidad_id' => $unidad->id]);

        $response->assertOk();
    }

    public function testGetActividadAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        $category = Category::factory()->create();
        $curso = Curso::factory()->create(['category_id' => $category->id]);

        $unidad = Unidad::factory()->create(['curso_id' => $curso->id]);

        $actividad = Actividad::factory()->create(['unidad_id' => $unidad->id]);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Actividad\GetActividad::class, ['id' => $actividad->id]);

        $response->assertOk();
    }
}

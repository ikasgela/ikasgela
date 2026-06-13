<?php

namespace Tests\Feature\Mcp;

use App\Models\Category;
use App\Models\Curso;
use App\Models\Organization;
use App\Models\Period;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Mcp\Server as McpServer;
use Laravel\Mcp\Server\Testing\PendingTestResponse;
use Tests\TestCase;

class LmsServerTest extends TestCase
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

    // ========== Organization Tools ==========

    public function testListOrganizations()
    {
        $this->actingAs($this->admin);

        Organization::factory()->count(3)->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\ListOrganizations::class);

        $response->assertOk();
    }

    public function testListOrganizationsReturnsData()
    {
        $this->actingAs($this->admin);

        $org = Organization::factory()->create(['name' => 'Test Org']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\ListOrganizations::class);

        $response->assertOk()->assertSee($org->name);
    }

    public function testGetOrganization()
    {
        $this->actingAs($this->admin);

        $org = Organization::factory()->create(['name' => 'Get Org']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\GetOrganization::class, ['id' => $org->id]);

        $response->assertOk()->assertSee($org->name);
    }

    public function testGetOrganizationNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\GetOrganization::class, ['id' => 9999]);

        $response->assertHasErrors(['No se encontró la organización']);
    }

    public function testCreateOrganization()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\CreateOrganization::class, [
                'name' => 'New Org',
                'seats' => 100,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('organizations', ['name' => 'New Org']);
    }

    public function testCreateOrganizationRequiresAdmin()
    {
        $this->actingAs($this->not_admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\CreateOrganization::class, [
                'name' => 'Unauthorized Org',
                'seats' => 100,
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testUpdateOrganization()
    {
        $this->actingAs($this->admin);

        $org = Organization::factory()->create(['name' => 'Old Name']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\UpdateOrganization::class, [
                'id' => $org->id,
                'name' => 'Updated Name',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('organizations', ['id' => $org->id, 'name' => 'Updated Name']);
    }

    public function testDeleteOrganization()
    {
        $this->actingAs($this->admin);

        $org = Organization::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\DeleteOrganization::class, ['id' => $org->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('organizations', ['id' => $org->id]);
    }

    public function testDeleteOrganizationRequiresAdmin()
    {
        $this->actingAs($this->not_admin);

        $org = Organization::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\DeleteOrganization::class, ['id' => $org->id]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    // ========== Period Tools ==========

    public function testListPeriods()
    {
        $this->actingAs($this->admin);

        Period::factory()->count(3)->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\ListPeriods::class);

        $response->assertOk();
    }

    public function testGetPeriod()
    {
        $this->actingAs($this->admin);

        $period = Period::factory()->create(['name' => 'Test Period']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\GetPeriod::class, ['id' => $period->id]);

        $response->assertOk()->assertSee($period->name);
    }

    public function testGetPeriodNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\GetPeriod::class, ['id' => 9999]);

        $response->assertHasErrors(['No se encontró el periodo']);
    }

    public function testCreatePeriod()
    {
        $this->actingAs($this->admin);

        $org = Organization::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\CreatePeriod::class, [
                'organization_id' => $org->id,
                'name' => 'New Period',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('periods', ['name' => 'New Period']);
    }

    public function testCreatePeriodRequiresValidOrganization()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\CreatePeriod::class, [
                'organization_id' => 9999,
                'name' => 'Bad Period',
            ]);

        $response->assertHasErrors(['No se encontró la organización']);
    }

    public function testUpdatePeriod()
    {
        $this->actingAs($this->admin);

        $period = Period::factory()->create(['name' => 'Old Period']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\UpdatePeriod::class, [
                'id' => $period->id,
                'name' => 'Updated Period',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('periods', ['id' => $period->id, 'name' => 'Updated Period']);
    }

    public function testDeletePeriod()
    {
        $this->actingAs($this->admin);

        $period = Period::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\DeletePeriod::class, ['id' => $period->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('periods', ['id' => $period->id]);
    }

    // ========== Category Tools ==========

    public function testListCategories()
    {
        $this->actingAs($this->admin);

        Category::factory()->count(3)->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\ListCategories::class);

        $response->assertOk();
    }

    public function testGetCategory()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\GetCategory::class, ['id' => $category->id]);

        $response->assertOk()->assertSee($category->name);
    }

    public function testGetCategoryNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\GetCategory::class, ['id' => 9999]);

        $response->assertHasErrors(['No se encontró la categoría']);
    }

    public function testCreateCategory()
    {
        $this->actingAs($this->admin);

        $period = Period::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\CreateCategory::class, [
                'period_id' => $period->id,
                'name' => 'New Category',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    public function testCreateCategoryRequiresValidPeriod()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\CreateCategory::class, [
                'period_id' => 9999,
                'name' => 'Bad Category',
            ]);

        $response->assertHasErrors(['No se encontró el periodo']);
    }

    public function testUpdateCategory()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create(['name' => 'Old Category']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\UpdateCategory::class, [
                'id' => $category->id,
                'name' => 'Updated Category',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Category']);
    }

    public function testDeleteCategory()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\DeleteCategory::class, ['id' => $category->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // ========== Curso Tools ==========

    public function testListCursos()
    {
        $this->actingAs($this->admin);

        Curso::factory()->count(3)->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\ListCursos::class);

        $response->assertOk();
    }

    public function testGetCurso()
    {
        $this->actingAs($this->admin);

        $curso = Curso::factory()->create(['nombre' => 'Test Curso']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\GetCurso::class, ['id' => $curso->id]);

        $response->assertOk()->assertSee($curso->nombre);
    }

    public function testGetCursoNotFound()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\GetCurso::class, ['id' => 9999]);

        $response->assertHasErrors(['No se encontró el curso']);
    }

    public function testCreateCurso()
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\CreateCurso::class, [
                'category_id' => $category->id,
                'nombre' => 'Nuevo Curso',
                'plazo_actividad' => 30,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('cursos', ['nombre' => 'Nuevo Curso']);
    }

    public function testCreateCursoRequiresValidCategory()
    {
        $this->actingAs($this->admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\CreateCurso::class, [
                'category_id' => 9999,
                'nombre' => 'Bad Curso',
                'plazo_actividad' => 30,
            ]);

        $response->assertHasErrors(['No se encontró la categoría']);
    }

    public function testUpdateCurso()
    {
        $this->actingAs($this->admin);

        $curso = Curso::factory()->create(['nombre' => 'Old Curso']);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\UpdateCurso::class, [
                'id' => $curso->id,
                'nombre' => 'Updated Curso',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('cursos', ['id' => $curso->id, 'nombre' => 'Updated Curso']);
    }

    public function testDeleteCurso()
    {
        $this->actingAs($this->admin);

        $curso = Curso::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\DeleteCurso::class, ['id' => $curso->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('cursos', ['id' => $curso->id]);
    }

    // ========== Authorization: non-admin cannot write ==========

    public function testNonAdminCannotCreateOrganization()
    {
        $this->actingAs($this->not_admin);

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\CreateOrganization::class, [
                'name' => 'Unauthorized',
                'seats' => 10,
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotUpdateOrganization()
    {
        $this->actingAs($this->not_admin);

        $org = Organization::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\UpdateOrganization::class, [
                'id' => $org->id,
                'name' => 'Hacked',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotCreatePeriod()
    {
        $this->actingAs($this->not_admin);

        $org = Organization::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\CreatePeriod::class, [
                'organization_id' => $org->id,
                'name' => 'Unauthorized Period',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotCreateCategory()
    {
        $this->actingAs($this->not_admin);

        $period = Period::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\CreateCategory::class, [
                'period_id' => $period->id,
                'name' => 'Unauthorized Category',
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    public function testNonAdminCannotCreateCurso()
    {
        $this->actingAs($this->not_admin);

        $category = Category::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\CreateCurso::class, [
                'category_id' => $category->id,
                'nombre' => 'Unauthorized Curso',
                'plazo_actividad' => 30,
            ]);

        $response->assertHasErrors(['Se requiere rol de administrador']);
    }

    // ========== Read tools accessible to all authenticated users ==========

    public function testListOrganizationsAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        Organization::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Organization\ListOrganizations::class);

        $response->assertOk();
    }

    public function testListPeriodsAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        Period::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Period\ListPeriods::class);

        $response->assertOk();
    }

    public function testListCategoriesAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        Category::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Category\ListCategories::class);

        $response->assertOk();
    }

    public function testListCursosAccessibleToProfesor()
    {
        $this->actingAs($this->profesor);

        Curso::factory()->create();

        $response = $this->mcp(\App\Mcp\Servers\LmsServer::class)
            ->tool(\App\Mcp\Tools\Curso\ListCursos::class);

        $response->assertOk();
    }
}

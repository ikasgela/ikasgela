<?php

namespace Tests\Feature\SafeExam;

use App\Models\AllowedApp;
use App\Models\Curso;
use App\Models\SafeExam;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AllowedAppControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $curso = null;

    private $required = [
        'title' => 'word',
        'executable' => 'word',
        'path' => 'word',
    ];

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
        $this->curso = Curso::factory()->create();
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $safe_exam = SafeExam::factory()->create([
            'curso_id' => $this->curso,
        ]);

        // When
        $response = $this->get(route('allowed_apps.create', [$safe_exam->id]));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New allowed app'), __('Save')]);
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->get(route('allowed_apps.create', [$safe_exam->id]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->make();
        $total = AllowedApp::all()->count();

        // When
        $this->post(route('allowed_apps.store'), $allowed_app->toArray());

        // Then
        $this->assertCount($total + 1, AllowedApp::all());
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $allowed_app = AllowedApp::factory()->make();

        // When
        $response = $this->post(route('allowed_apps.store'), $allowed_app->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $empty = new AllowedApp();
        foreach ($this->required as $field => $faker) {
            $empty->$field = fake()->$faker();
        }

        // When
        $response = $this->post(route('allowed_apps.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->make([$field => null]);

        // When
        $response = $this->post(route('allowed_apps.store'), $allowed_app->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testStoreTestingNotRequiredFields()
    {
        foreach ($this->required as $field => $faker) {
            $this->storeRequires($field);
        }
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->create();

        // When
        $response = $this->get(route('allowed_apps.edit', $allowed_app), $allowed_app->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$allowed_app->executable, __('Save')]);
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $allowed_app = AllowedApp::factory()->create();

        // When
        $response = $this->get(route('allowed_apps.edit', $allowed_app), $allowed_app->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->create();
        $allowed_app->executable = fake()->filePath();

        // When
        $this->put(route('allowed_apps.update', $allowed_app), $allowed_app->toArray());

        // Then
        $this->assertDatabaseHas('allowed_apps', ['id' => $allowed_app->id, 'executable' => $allowed_app->executable]);
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $allowed_app = AllowedApp::factory()->create();
        $allowed_app->executable = fake()->filePath();

        // When
        $response = $this->put(route('allowed_apps.update', $allowed_app), $allowed_app->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->create();
        $empty = new AllowedApp();
        foreach ($this->required as $field => $faker) {
            $empty->$field = fake()->$faker();
        }

        // When
        $response = $this->put(route('allowed_apps.update', $allowed_app), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->create();
        $allowed_app->$field = null;

        // When
        $response = $this->put(route('allowed_apps.update', $allowed_app), $allowed_app->toArray());

        // Then
        $response->assertSessionHasErrors($field);
    }

    public function testUpdateTestingNotRequiredFields()
    {
        foreach ($this->required as $field => $faker) {
            $this->updateRequires($field);
        }
    }

    public function testDelete()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_app = AllowedApp::factory()->create();

        // When
        $this->delete(route('allowed_apps.destroy', $allowed_app));

        // Then
        $this->assertDatabaseMissing('allowed_apps', $allowed_app->toArray());
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $allowed_app = AllowedApp::factory()->create();

        // When
        $response = $this->delete(route('allowed_apps.destroy', $allowed_app));

        // Then
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\AllowedUrl;
use App\Models\Curso;
use App\Models\SafeExam;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AllowedUrlControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $curso = null;

    private $required = [
        'url' => 'url',
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
        $response = $this->get(route('allowed_urls.create', [$safe_exam->id]));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New allowed URL'), __('Save')]);
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->get(route('allowed_urls.create', [$safe_exam->id]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_url = AllowedUrl::factory()->make();
        $total = AllowedUrl::all()->count();

        // When
        $this->post(route('allowed_urls.store'), $allowed_url->toArray());

        // Then
        $this->assertCount($total + 1, AllowedUrl::all());
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $allowed_url = AllowedUrl::factory()->make();

        // When
        $response = $this->post(route('allowed_urls.store'), $allowed_url->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $empty = new AllowedUrl();
        foreach ($this->required as $field => $faker) {
            $empty->$field = fake()->$faker();
        }

        // When
        $response = $this->post(route('allowed_urls.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_url = AllowedUrl::factory()->make([$field => null]);

        // When
        $response = $this->post(route('allowed_urls.store'), $allowed_url->toArray());

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
        $allowed_url = AllowedUrl::factory()->create();

        // When
        $response = $this->get(route('allowed_urls.edit', $allowed_url), $allowed_url->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$allowed_url->url, __('Save')]);
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $allowed_url = AllowedUrl::factory()->create();

        // When
        $response = $this->get(route('allowed_urls.edit', $allowed_url), $allowed_url->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_url = AllowedUrl::factory()->create();
        $allowed_url->url = fake()->url();

        // When
        $this->put(route('allowed_urls.update', $allowed_url), $allowed_url->toArray());

        // Then
        $this->assertDatabaseHas('allowed_urls', ['id' => $allowed_url->id, 'url' => $allowed_url->url]);
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $allowed_url = AllowedUrl::factory()->create();
        $allowed_url->url = fake()->url();

        // When
        $response = $this->put(route('allowed_urls.update', $allowed_url), $allowed_url->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_url = AllowedUrl::factory()->create();
        $empty = new AllowedUrl();
        foreach ($this->required as $field => $faker) {
            $empty->$field = fake()->$faker();
        }

        // When
        $response = $this->put(route('allowed_urls.update', $allowed_url), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $allowed_url = AllowedUrl::factory()->create();
        $allowed_url->$field = null;

        // When
        $response = $this->put(route('allowed_urls.update', $allowed_url), $allowed_url->toArray());

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
        $allowed_url = AllowedUrl::factory()->create();

        // When
        $this->delete(route('allowed_urls.destroy', $allowed_url));

        // Then
        $this->assertDatabaseMissing('allowed_urls', $allowed_url->toArray());
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $allowed_url = AllowedUrl::factory()->create();

        // When
        $response = $this->delete(route('allowed_urls.destroy', $allowed_url));

        // Then
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Evaluacion;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FeedbacksCRUDTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
        'mensaje', 'comentable_id'
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
        $feedback = Feedback::factory()->create();
        setting_usuario(['curso_actual' => $feedback->comentable_id]);

        // When
        $response = $this->get(route('feedbacks.index'));

        // Then
        $response->assertSuccessful()->assertSee($feedback->titulo);
    }

    public function testNotAdminNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('feedbacks.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('feedbacks.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        // When
        $response = $this->get(route('feedbacks.create'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('New course feedback message'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        // When
        $response = $this->get(route('feedbacks.create'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Auth
        // Given
        // When
        $response = $this->get(route('feedbacks.create'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $feedback = Feedback::factory()->make();
        $total = Feedback::all()->count();

        // When
        $this->post(route('feedbacks.store'), $feedback->toArray());

        // Then
        $this->assertEquals($total + 1, Feedback::all()->count());
    }

    public function testNotAdminNotStore()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $feedback = Feedback::factory()->make();

        // When
        $response = $this->post(route('feedbacks.store'), $feedback->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Auth
        // Given
        $feedback = Feedback::factory()->make();

        // When
        $response = $this->post(route('feedbacks.store'), $feedback->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testStoreUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $total = Feedback::all()->count();

        $empty = new Feedback();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->post(route('feedbacks.store'), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function storeRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $feedback = Feedback::factory()->make([$field => null]);

        // When
        $response = $this->post(route('feedbacks.store'), $feedback->toArray());

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
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->get(route('feedbacks.show', $feedback));

        // Then
        $response->assertStatus(404);
    }

    public function testNotAdminNotShow()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->get(route('feedbacks.show', $feedback));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->get(route('feedbacks.show', $feedback));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->get(route('feedbacks.edit', $feedback), $feedback->toArray());

        // Then
        $response->assertSuccessful()->assertSeeInOrder([$feedback->mensaje, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->get(route('feedbacks.edit', $feedback), $feedback->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->get(route('feedbacks.edit', $feedback), $feedback->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $feedback = Feedback::factory()->create();
        $feedback->mensaje = "Updated";

        // When
        $this->put(route('feedbacks.update', $feedback), $feedback->toArray());

        // Then
        $this->assertDatabaseHas('feedback', ['id' => $feedback->id, 'mensaje' => $feedback->mensaje]);
    }

    public function testNotAdminNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $feedback = Feedback::factory()->create();
        $feedback->mensaje = "Updated";

        // When
        $response = $this->put(route('feedbacks.update', $feedback), $feedback->toArray());

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $feedback = Feedback::factory()->create();
        $feedback->mensaje = "Updated";

        // When
        $response = $this->put(route('feedbacks.update', $feedback), $feedback->toArray());

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateUntestedRequiredFields()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $feedback = Feedback::factory()->create();
        $empty = new Feedback();
        foreach ($this->required as $field) {
            $empty->$field = '0';
        }

        // When
        $response = $this->put(route('feedbacks.update', $feedback), $empty->toArray());

        // Then
        $response->assertSessionHasNoErrors();
    }

    private function updateRequires(string $field)
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $feedback = Feedback::factory()->create();
        $feedback->$field = null;

        // When
        $response = $this->put(route('feedbacks.update', $feedback), $feedback->toArray());

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
        $feedback = Feedback::factory()->create();

        // When
        $this->delete(route('feedbacks.destroy', $feedback));

        // Then
        $this->assertDatabaseMissing('feedback', $feedback->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->delete(route('feedbacks.destroy', $feedback));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Auth
        // Given
        $feedback = Feedback::factory()->create();

        // When
        $response = $this->delete(route('feedbacks.destroy', $feedback));

        // Then
        $response->assertRedirect(route('login'));
    }
}

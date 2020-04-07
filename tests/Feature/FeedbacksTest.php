<?php

namespace Tests\Feature;

use App\Feedback;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FeedbacksTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();

        // When
        $response = $this->get(route('feedbacks.index'));

        // Then
        $response->assertSee($feedback->mensaje);
    }

    public function testNotAdminNotIndex()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('feedbacks.index'))
            ->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // Given
        // When
        // Then
        $this->get(route('feedbacks.index'))
            ->assertRedirect(route('login'));
    }

    public function testCreate()
    {
        // Given
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('feedbacks.create'));

        // Then
        $response->assertSeeInOrder([__('New feedback message'), __('Save')]);
    }

    public function testNotAdminNotCreate()
    {
        // Given
        $this->actingAs($this->not_admin);

        // When
        // Then
        $this->get(route('feedbacks.create'))
            ->assertForbidden();
    }

    public function testNotAuthNotCreate()
    {
        // Given
        // When
        // Then
        $this->get(route('feedbacks.create'))
            ->assertRedirect(route('login'));
    }

    public function testStore()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->make();
        $total = Feedback::all()->count();

        // When
        $this->post(route('feedbacks.store'), $feedback->toArray());

        // Then
        $this->assertCount($total + 1, Feedback::all());
    }

    public function testNotAdminNotStore()
    {
        // Given
        $this->actingAs($this->not_admin);
        $feedback = factory(Feedback::class)->make();

        // When
        // Then
        $this->post(route('feedbacks.store'), $feedback->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotStore()
    {
        // Given
        $feedback = factory(Feedback::class)->make();

        // When
        // Then
        $this->post(route('feedbacks.store'), $feedback->toArray())
            ->assertRedirect(route('login'));
    }

    public function testStoreRequiresMensaje()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->make(['mensaje' => null]);

        // When
        // Then
        $this->post(route('feedbacks.store'), $feedback->toArray())
            ->assertSessionHasErrors('mensaje');
    }

    public function testStoreRequiresCurso()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->make(['curso_id' => null]);

        // When
        // Then
        $this->post(route('feedbacks.store'), $feedback->toArray())
            ->assertSessionHasErrors('curso_id');
    }

    public function testShow()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();

        // When
        $response = $this->get(route('feedbacks.show', $feedback));

        // Then
        $response->assertSee(__('Not implemented.'));
    }

    public function testNotAdminNotShow()
    {
        // Given
        $this->actingAs($this->not_admin);
        $feedback = factory(Feedback::class)->create();

        // When
        // Then
        $this->get(route('feedbacks.show', $feedback))
            ->assertForbidden();
    }

    public function testNotAuthNotShow()
    {
        // Given
        $feedback = factory(Feedback::class)->create();

        // When
        // Then
        $this->get(route('feedbacks.show', $feedback))
            ->assertRedirect(route('login'));
    }

    public function testEdit()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();

        // When
        $response = $this->get(route('feedbacks.edit', $feedback), $feedback->toArray());

        // Then
        $response->assertSeeInOrder([$feedback->mensaje, $feedback->slug, __('Save')]);
    }

    public function testNotAdminNotEdit()
    {
        // Given
        $this->actingAs($this->not_admin);
        $feedback = factory(Feedback::class)->create();

        // When
        // Then
        $this->get(route('feedbacks.edit', $feedback), $feedback->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Given
        $feedback = factory(Feedback::class)->create();

        // When
        // Then
        $this->get(route('feedbacks.edit', $feedback), $feedback->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();
        $feedback->mensaje = "Updated";

        // When
        $this->put(route('feedbacks.update', $feedback), $feedback->toArray());

        // Then
        $this->assertDatabaseHas('feedback', ['id' => $feedback->id, 'mensaje' => $feedback->mensaje]);
    }

    public function testNotAdminNotUpdate()
    {
        // Given
        $this->actingAs($this->not_admin);
        $feedback = factory(Feedback::class)->create();
        $feedback->mensaje = "Updated";

        // When
        // Then
        $this->put(route('feedbacks.update', $feedback), $feedback->toArray())
            ->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Given
        $feedback = factory(Feedback::class)->create();
        $feedback->mensaje = "Updated";

        // When
        // Then
        $this->put(route('feedbacks.update', $feedback), $feedback->toArray())
            ->assertRedirect(route('login'));
    }

    public function testUpdateRequiresMensaje()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();

        // When
        $feedback->mensaje = null;

        // Then
        $this->put(route('feedbacks.update', $feedback), $feedback->toArray())
            ->assertSessionHasErrors('mensaje');
    }

    public function testUpdateRequiresCurso()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();

        // When
        $feedback->curso_id = null;

        // Then
        $this->put(route('feedbacks.update', $feedback), $feedback->toArray())
            ->assertSessionHasErrors('curso_id');
    }

    public function testDelete()
    {
        // Given
        $this->actingAs($this->admin);
        $feedback = factory(Feedback::class)->create();

        // When
        $this->delete(route('feedbacks.destroy', $feedback));

        // Then
        $this->assertDatabaseMissing('feedback', $feedback->toArray());
    }

    public function testNotAdminNotDelete()
    {
        // Given
        $this->actingAs($this->not_admin);
        $feedback = factory(Feedback::class)->create();

        // When
        // Then
        $this->delete(route('feedbacks.destroy', $feedback))
            ->assertForbidden();
    }

    public function testNotAuthNotDelete()
    {
        // Given
        $feedback = factory(Feedback::class)->create();

        // When
        // Then
        $this->delete(route('feedbacks.destroy', $feedback))
            ->assertRedirect(route('login'));
    }
}

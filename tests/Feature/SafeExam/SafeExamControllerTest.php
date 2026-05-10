<?php

namespace Tests\Feature\SafeExam;

use Override;
use App\Models\Curso;
use App\Models\SafeExam;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SafeExamControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->admin);

        // When
        $response = $this->get(route('safe_exam.index'));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Safe Exam Browser')]);
    }

    public function testNotAuthNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // When
        $response = $this->get(route('safe_exam.index'));

        // Then
        $response->assertForbidden();
    }

    public function testResetToken()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('safe_exam.reset_token', $curso));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('safe_exams', ['curso_id' => $curso->id]);
        $this->assertNotEmpty(SafeExam::where('curso_id', $curso->id)->first()->token);
    }

    public function testNotAuthNotResetToken()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('safe_exam.reset_token', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testDeleteToken()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();
        $safe_exam = SafeExam::factory()->create(['curso_id' => $curso->id]);
        $this->assertNotEmpty($safe_exam->token);

        // When
        $response = $this->delete(route('safe_exam.delete_token', $curso));

        // Then
        $response->assertRedirect();
        $safe_exam->refresh();
        $this->assertEmpty($safe_exam->token);
    }

    public function testNotAuthNotDeleteToken()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->delete(route('safe_exam.delete_token', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testResetQuitPassword()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('safe_exam.reset_quit_password', $curso));

        // Then
        $response->assertRedirect();
        $this->assertDatabaseHas('safe_exams', ['curso_id' => $curso->id]);
        $this->assertNotEmpty(SafeExam::where('curso_id', $curso->id)->first()->quit_password);
    }

    public function testNotAuthNotResetQuitPassword()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('safe_exam.reset_quit_password', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testDeleteQuitPassword()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $curso = Curso::factory()->create();
        $safe_exam = SafeExam::factory()->create(['curso_id' => $curso->id]);
        $this->assertNotEmpty($safe_exam->quit_password);

        // When
        $response = $this->delete(route('safe_exam.delete_quit_password', $curso));

        // Then
        $response->assertRedirect();
        $safe_exam->refresh();
        $this->assertEmpty($safe_exam->quit_password);
    }

    public function testNotAuthNotDeleteQuitPassword()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $curso = Curso::factory()->create();

        // When
        $response = $this->delete(route('safe_exam.delete_quit_password', $curso));

        // Then
        $response->assertForbidden();
    }

    public function testAllowed()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->get(route('safe_exam.allowed', $safe_exam));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAuthNotAllowed()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->get(route('safe_exam.allowed', $safe_exam));

        // Then
        $response->assertForbidden();
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->get(route('safe_exam.configure', $safe_exam));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Configure options')]);
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->get(route('safe_exam.configure', $safe_exam));

        // Then
        $response->assertForbidden();
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $safe_exam = SafeExam::factory()->create(['full_screen' => false, 'show_toolbar' => false]);

        // When
        $response = $this->put(route('safe_exam.update', $safe_exam), [
            'full_screen' => 'on',
        ]);

        // Then
        $response->assertRedirect(route('safe_exam.index'));
        $this->assertDatabaseHas('safe_exams', ['id' => $safe_exam->id, 'full_screen' => true]);
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin);

        // Given
        $safe_exam = SafeExam::factory()->create();

        // When
        $response = $this->put(route('safe_exam.update', $safe_exam), []);

        // Then
        $response->assertForbidden();
    }

    public function testExitSeb()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given a quit_password hash (no admin needed)
        $hash = hash('sha256', 'some_password');

        // When
        $response = $this->get(route('safe_exam.exit_seb', $hash));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Exiting Safe Exam Browser')]);
    }

    public function testConfigSeb()
    {
        // config_seb is public (no auth), but needs a SafeExam
        $safe_exam = \App\Models\SafeExam::factory()->create([
            'full_screen' => true,
            'show_toolbar' => false,
        ]);
        $curso = $safe_exam->curso;

        $response = $this->get(route('safe_exam.config_seb', $curso));

        $response->assertSuccessful();
        $this->assertStringContainsString('<?xml', $response->streamedContent());
    }

    public function testConfigSebWithAllowedApps()
    {
        $safe_exam = \App\Models\SafeExam::factory()->create([
            'full_screen' => false,
            'show_toolbar' => true,
        ]);
        $curso = $safe_exam->curso;
        \App\Models\AllowedApp::factory()->create(['safe_exam_id' => $safe_exam->id, 'disabled' => false]);
        \App\Models\AllowedUrl::factory()->create(['safe_exam_id' => $safe_exam->id, 'disabled' => false, 'url' => 'https://example.com/path']);

        $response = $this->get(route('safe_exam.config_seb', $curso));

        $response->assertSuccessful();
    }

    public function testConfigSebCursoWithNoSafeExam()
    {
        $curso = \App\Models\Curso::factory()->create();

        $response = $this->get(route('safe_exam.config_seb', $curso));

        $response->assertNotFound();
    }

    public function testGetDomain()
    {
        $controller = new \App\Http\Controllers\SafeExamController();
        $result = $controller->get_domain('www.example.com');
        $this->assertEquals('example.com', $result);
    }
}

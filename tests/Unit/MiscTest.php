<?php

namespace Tests\Unit;

use App\Events\GiteaRepoForked;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Requests\StoreFile;
use App\Models\CacheClear;
use App\Models\Curso;
use App\Models\IntellijProject;
use App\Models\Milestone;
use App\Models\User;
use App\Observers\CursoObserver;
use App\Observers\UserObserver;
use App\Policies\MilestonePolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class MiscTest extends TestCase
{
    use DatabaseTransactions;

    // ===== GiteaRepoForked Event =====
    public function testGiteaRepoForkedConstruct()
    {
        $project = IntellijProject::factory()->create();
        $event = new GiteaRepoForked($project);
        $this->assertEquals($project->id, $event->intellij_project->id);
    }

    public function testGiteaRepoForkedBroadcastOn()
    {
        $project = IntellijProject::factory()->create();
        $event = new GiteaRepoForked($project);
        $channels = $event->broadcastOn();
        $this->assertIsArray($channels);
        $this->assertCount(1, $channels);
    }

    // ===== MilestonePolicy =====
    public function testMilestonePolicyViewAny()
    {
        $user = User::factory()->create();
        $policy = new MilestonePolicy();
        $result = $policy->viewAny($user);
        $this->assertNull($result);
    }

    public function testMilestonePolicyView()
    {
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        $policy = new MilestonePolicy();
        $result = $policy->view($user, $milestone);
        $this->assertNull($result);
    }

    public function testMilestonePolicyCreate()
    {
        $user = User::factory()->create();
        $policy = new MilestonePolicy();
        $result = $policy->create($user);
        $this->assertNull($result);
    }

    public function testMilestonePolicyUpdate()
    {
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        $policy = new MilestonePolicy();
        $result = $policy->update($user, $milestone);
        $this->assertNull($result);
    }

    public function testMilestonePolicyDelete()
    {
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        $policy = new MilestonePolicy();
        $result = $policy->delete($user, $milestone);
        $this->assertNull($result);
    }

    public function testMilestonePolicyRestore()
    {
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        $policy = new MilestonePolicy();
        $result = $policy->restore($user, $milestone);
        $this->assertNull($result);
    }

    public function testMilestonePolicyForceDelete()
    {
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $milestone = Milestone::factory()->create(['curso_id' => $curso->id]);
        $policy = new MilestonePolicy();
        $result = $policy->forceDelete($user, $milestone);
        $this->assertNull($result);
    }

    // ===== RedirectIfAuthenticated Middleware =====
    public function testRedirectIfAuthenticatedUnauthenticated()
    {
        $middleware = new RedirectIfAuthenticated();
        $request = Request::create('/login', 'GET');
        $next = fn($req) => response('ok');
        $response = $middleware->handle($request, $next);
        $this->assertEquals('ok', $response->getContent());
    }

    public function testRedirectIfAuthenticatedAuthenticated()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $middleware = new RedirectIfAuthenticated();
        $request = Request::create('/login', 'GET');
        $next = fn($req) => response('ok');
        $response = $middleware->handle($request, $next);
        $this->assertEquals(302, $response->getStatusCode());
    }

    // ===== StoreFile FormRequest =====
    public function testStoreFileAuthorize()
    {
        $request = new StoreFile();
        $this->assertTrue($request->authorize());
    }

    public function testStoreFileRulesAdmin()
    {
        $this->crearUsuarios();
        $this->actingAs($this->admin);
        $request = new StoreFile();
        $rules = $request->rules();
        $this->assertArrayHasKey('file', $rules);
        $this->assertEquals('required', $rules['file']);
    }

    public function testStoreFileRulesNonAdmin()
    {
        $this->crearUsuarios();
        $this->actingAs($this->alumno);
        $request = new StoreFile();
        $rules = $request->rules();
        $this->assertArrayHasKey('file', $rules);
        $this->assertStringContainsString('mimes:', $rules['file']);
    }

    // ===== CacheClear model =====
    public function testCacheClearUser()
    {
        $user = User::factory()->create();
        $cacheClear = CacheClear::create([
            'user_id' => $user->id,
            'fecha' => now(),
        ]);
        $this->assertEquals($user->id, $cacheClear->user->id);
    }

    // ===== CursoObserver =====
    public function testCursoObserverDeleted()
    {
        $observer = new CursoObserver();
        $curso = Curso::factory()->create();
        $observer->deleted($curso);
        $this->assertTrue(true); // No exception thrown
    }

    // ===== UserObserver =====
    public function testUserObserverDeleted()
    {
        $observer = new UserObserver();
        $user = User::factory()->create();
        $observer->deleted($user);
        $this->assertTrue(true); // No exception thrown
    }

    public function testUserObserverDeleting()
    {
        $observer = new UserObserver();
        $user = User::factory()->create();
        $observer->deleting($user);
        $this->assertTrue(true); // No exception thrown
    }
}

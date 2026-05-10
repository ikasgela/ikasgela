<?php

namespace Tests\Unit;

use App\Http\Controllers\CursoController;
use App\Http\Controllers\IntellijProjectController;
use App\Models\MarkdownText;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class ControllersAndModelsCoverageTest extends TestCase
{
    use DatabaseTransactions;

    public function testCursoControllerPrivateClonarRepositorioRunsCommands()
    {
        Process::fake([
            '*' => Process::result(output: 'ok', exitCode: 0),
        ]);

        $controller = new CursoController();
        $repo = [
            'path_with_namespace' => 'owner/repo',
            'owner' => 'owner',
            'name' => 'repo',
        ];
        $path = sys_get_temp_dir() . '/curso-clone-' . bin2hex(random_bytes(4));
        mkdir($path, 0777, true);

        $method = new \ReflectionMethod($controller, 'clonarRepositorio');
        $method->setAccessible(true);
        $method->invoke($controller, $path, $repo);

        Process::assertRan(fn($p) => str_contains($p->command, 'git clone http://root:') && str_contains($p->command, 'owner/repo.git owner@repo'));
        Process::assertRan(fn($p) => $p->command === 'git remote remove origin');

        @rmdir($path);
    }

    public function testIntellijProjectControllerPrivateClonarRepositorioRunsCommands()
    {
        Process::fake([
            '*' => Process::result(output: 'ok', exitCode: 0),
        ]);

        $controller = new IntellijProjectController();
        $repo = [
            'path_with_namespace' => 'owner/repo',
            'owner' => 'owner',
            'name' => 'repo',
        ];
        $path = sys_get_temp_dir() . '/intellij-clone-' . bin2hex(random_bytes(4));
        mkdir($path, 0777, true);

        $method = new \ReflectionMethod($controller, 'clonarRepositorio');
        $method->setAccessible(true);
        $method->invoke($controller, $path, $repo);

        Process::assertRan(fn($p) => str_contains($p->command, 'git clone http://root:') && str_contains($p->command, 'owner/repo.git owner@repo'));
        Process::assertRan(fn($p) => $p->command === 'git remote remove origin');

        @rmdir($path);
    }

    public function testIntellijProjectControllerZipDirectoryWithSubdirsReturnsDownloadResponse()
    {
        Storage::fake('temp');
        Storage::disk('temp')->put('/zipdir/a.txt', 'A');
        Storage::disk('temp')->put('/zipdir/nested/b.txt', 'B');

        $controller = new IntellijProjectController();
        $response = $controller->zipDirectoryWithSubdirs('bundle.zip', '/zipdir');

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertFileExists(Storage::disk('temp')->path('bundle.zip'));
    }

    public function testMarkdownTextRawReturnsFallbackWithoutGitea()
    {
        config(['ikasgela.gitea_enabled' => false]);

        $markdown = MarkdownText::factory()->create([
            'repositorio' => 'owner/repo',
            'archivo' => 'README.md',
            'rama' => 'main',
        ]);

        $result = $markdown->raw();

        $this->assertStringContainsString('Error', $result);
    }
}


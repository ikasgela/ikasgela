<?php

namespace Tests\Browser\Sitio;

use App\Models\YoutubeVideo;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Phase 6: Admin resource management overview.
 * Tests basic admin CRUD for various resource types.
 */
class T11_AdminRecursosTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private static ?int $youtubeVideoId = null;

    // --- Login ---

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'admin@ikasgela.com', '12345Abcde', 'admin.index');
        });
    }

    // --- YouTube Videos CRUD ---

    public function testCrearYoutubeVideo(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('youtube_videos.index'));
            $browser->visit(route('youtube_videos.create'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('New YouTube video'));

            $browser->type('titulo', '__Dusk_YT_Video__');
            $browser->type('descripcion', 'Video de test Dusk');
            $browser->type('codigo', 'dQw4w9WgXcQ');
            $browser->press(__('Save'));

            // After store, should redirect to index
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_YT_Video__');

            self::$youtubeVideoId = YoutubeVideo::where('titulo', '__Dusk_YT_Video__')->value('id');
        });
    }

    public function testEditarYoutubeVideo(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('youtube_videos.index'));
            $browser->visit(route('youtube_videos.edit', self::$youtubeVideoId));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Edit YouTube video'));

            $browser->clear('titulo');
            $browser->type('titulo', '__Dusk_YT_Video_Editado__');
            $browser->press(__('Save'));

            $browser->assertRouteIs('youtube_videos.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_YT_Video_Editado__');
        });
    }

    public function testEliminarYoutubeVideo(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('youtube_videos.index'));
            $this->assertNoAppErrors($browser);

            $id = self::$youtubeVideoId;
            // Find and delete using form
            $browser->script(
                "document.querySelector(\"form[action='" . route('youtube_videos.destroy', $id) . "']\").querySelector('[name=borrar]').click();"
            );
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('youtube_videos.index');
            $this->assertNoAppErrors($browser);
            $browser->assertDontSee('__Dusk_YT_Video_Editado__');
        });
    }

    // --- Navigate resources list ---

    public function testRecursosMarkdown(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('markdown_texts.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Resources: Markdown texts'));
        });
    }

    public function testRecursosYoutube(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('youtube_videos.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Resources: YouTube videos'));
        });
    }

    public function testRecursosFicheros(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('file_resources.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Resources: Files'));
        });
    }

    public function testRecursosEnlaces(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('link_collections.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Resources: Link collections'));
        });
    }

    public function testRecursosCuestionarios(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('cuestionarios.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Questionnaires'));
        });
    }

    public function testRecursosImagenes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('file_uploads.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Resources: Image uploads'));
        });
    }

    public function testRecursosRubricas(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('rubrics.index'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Rubrics'));
        });
    }

    // --- Cleanup ---

    public function testTeardown(): void
    {
        if (self::$youtubeVideoId) {
            YoutubeVideo::find(self::$youtubeVideoId)?->delete();
        }
        $this->assertTrue(true);
    }

    // --- Logout ---

    public function testLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }
}

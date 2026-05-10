<?php

namespace Tests\Browser\Sitio;

use App\Models\Category;
use App\Models\Period;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Phase 3: Admin CRUD for structure entities (periods & categories).
 * Tests create/edit/delete flows for the two core organizational entities.
 * Uses retornar() pattern: visit index before create/edit.
 */
class T8_EstructuraCRUDTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private static ?int $periodId = null;
    private static ?int $categoryId = null;

    // --- Login ---

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'admin@ikasgela.com', '12345Abcde', 'admin.index');
        });
    }

    // --- Period CRUD ---

    public function testCrearPeriodo(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('periods.index'));
            $browser->visit(route('periods.create'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('New period'));

            $browser->select('organization_id', '1');
            $browser->type('name', '__Dusk_P8__');
            $browser->type('slug', 'dusk-p8');
            $browser->press(__('Save'));

            $browser->assertRouteIs('periods.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_P8__');

            self::$periodId = Period::where('slug', 'dusk-p8')->value('id');
        });
    }

    public function testEditarPeriodo(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('periods.index'));
            $browser->visit(route('periods.edit', self::$periodId));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Edit period'));

            $browser->clear('name');
            $browser->type('name', '__Dusk_P8_Editado__');
            $browser->clear('slug');
            $browser->type('slug', 'dusk-p8-editado');
            $browser->press(__('Save'));

            $browser->assertRouteIs('periods.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_P8_Editado__');
        });
    }

    public function testEliminarPeriodo(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('periods.index'));
            $this->assertNoAppErrors($browser);

            $id = self::$periodId;
            $browser->script("document.querySelector('form[action*=\"periods/{$id}\"]').querySelector('[name=borrar]').click();");
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('periods.index');
            $this->assertNoAppErrors($browser);
            $browser->assertDontSee('__Dusk_P8_Editado__');
        });
    }

    // --- Category CRUD ---

    public function testCrearCategoria(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('categories.index'));
            $browser->visit(route('categories.create'));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('New category'));

            $browser->select('period_id', '1');
            $browser->type('name', '__Dusk_C8__');
            $browser->type('slug', 'dusk-c8');
            $browser->press(__('Save'));

            $browser->assertRouteIs('categories.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_C8__');

            self::$categoryId = Category::where('slug', 'dusk-c8')->value('id');
        });
    }

    public function testEditarCategoria(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('categories.index'));
            $browser->visit(route('categories.edit', self::$categoryId));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Edit category'));

            $browser->clear('name');
            $browser->type('name', '__Dusk_C8_Editado__');
            $browser->clear('slug');
            $browser->type('slug', 'dusk-c8-editado');
            $browser->press(__('Save'));

            $browser->assertRouteIs('categories.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('__Dusk_C8_Editado__');
        });
    }

    public function testEliminarCategoria(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('categories.index'));
            $this->assertNoAppErrors($browser);

            $id = self::$categoryId;
            $browser->script("document.querySelector('form[action*=\"categories/{$id}\"]').querySelector('[name=borrar]').click();");
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('categories.index');
            $this->assertNoAppErrors($browser);
            $browser->assertDontSee('__Dusk_C8_Editado__');
        });
    }

    // --- Teardown safety ---

    public function testTeardown(): void
    {
        if (self::$periodId) {
            Period::find(self::$periodId)?->delete();
        }
        if (self::$categoryId) {
            Category::find(self::$categoryId)?->delete();
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

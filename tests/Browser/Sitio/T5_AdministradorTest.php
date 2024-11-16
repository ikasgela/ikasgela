<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T5_AdministradorTest extends DuskTestCase
{
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'));
            $browser->type('email', 'admin@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('admin.index');
            $browser->assertDontSee('Ignition');
            $browser->assertDontSee('403');
        });
    }

    // Actividades

    public function testActividadesPlantillas()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('actividades.plantillas'));
            $browser->assertRouteIs('actividades.plantillas');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities'));
        });
    }

    public function testActividadesClonador()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('intellij_projects.copia'));
            $browser->assertRouteIs('intellij_projects.copia');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Project cloner'));
        });
    }

    public function testActividadesDescargar()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('intellij_projects.descargar'));
            $browser->assertRouteIs('intellij_projects.descargar');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Download projects'));
        });
    }

    // Recursos

    public function testRecursosMarkdown()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('markdown_texts.index'));
            $browser->assertRouteIs('markdown_texts.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Resources: Markdown texts'));
        });
    }

    public function testRecursosYoutube()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('youtube_videos.index'));
            $browser->assertRouteIs('youtube_videos.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Resources: YouTube videos'));
        });
    }

    public function testRecursosEnlaces()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('link_collections.index'));
            $browser->assertRouteIs('link_collections.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Resources: Link collections'));
        });
    }

    public function testRecursosFicheros()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('file_resources.index'));
            $browser->assertRouteIs('file_resources.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Resources: Files'));
        });
    }

    public function testRecursosCuestionarios()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('cuestionarios.index'));
            $browser->assertRouteIs('cuestionarios.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Questionnaires'));
        });
    }

    public function testRecursosImagenes()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('file_uploads.index'));
            $browser->assertRouteIs('file_uploads.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Resources: Image uploads'));
        });
    }

    public function testRecursosIntellij()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('intellij_projects.index'));
            $browser->assertRouteIs('intellij_projects.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Resources: IntelliJ projects'));
        });
    }

    public function testRecursosSelectores()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('selectors.index'));
            $browser->assertRouteIs('selectors.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Selectors'));
        });
    }

    // Estructura

    public function testEstructuraOrganizaciones()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('organizations.index'));
            $browser->assertRouteIs('organizations.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Organizations'));
        });
    }

    public function testEstructuraPeriodos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('periods.index'));
            $browser->assertRouteIs('periods.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Periods'));
        });
    }

    public function testEstructuraCategorias()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('categories.index'));
            $browser->assertRouteIs('categories.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Categories'));
        });
    }

    public function testEstructuraCursos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('cursos.index'));
            $browser->assertRouteIs('cursos.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Courses'));
        });
    }

    public function testEstructuraUnidades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('unidades.index'));
            $browser->assertRouteIs('unidades.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Units'));
        });
    }

    public function testEstructuraActividades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('actividades.index'));
            $browser->assertRouteIs('actividades.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities'));
        });
    }

    // Usuarios

    public function testUsuariosGrupos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('groups.index'));
            $browser->assertRouteIs('groups.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Groups'));
        });
    }

    public function testUsuariosEquipos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('teams.index'));
            $browser->assertRouteIs('teams.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Teams'));
        });
    }

    public function testUsuariosUsuarios()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.index'));
            $browser->assertRouteIs('users.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Users'));
        });
    }

    public function testUsuariosRoles()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('roles.index'));
            $browser->assertRouteIs('roles.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Roles'));
        });
    }

    // Evaluación

    public function testEvaluacionCualificaciones()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('qualifications.index'));
            $browser->assertRouteIs('qualifications.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Qualifications'));
        });
    }

    public function testEvaluacionCompetencias()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('skills.index'));
            $browser->assertRouteIs('skills.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Skills'));
        });
    }

    public function testEvaluacionFeedback()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('feedbacks.index'));
            $browser->assertRouteIs('feedbacks.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Feedback messages'));
        });
    }

    public function testEvaluacionEvaluaciones()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('milestones.index'));
            $browser->assertRouteIs('milestones.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Milestones'));
        });
    }

    public function testEvaluacionSafeexam()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('safe_exam.index'));
            $browser->assertRouteIs('safe_exam.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Safe Exam Browser'));
        });
    }

    // Registros

    public function testRegistros()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('registros.index'));
            $browser->assertRouteIs('registros.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Records'));
        });
    }

    public function testLogs()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('logs'));
            $browser->assertRouteIs('logs');
            $browser->assertDontSee('Ignition');
            $browser->assertSee('Laravel Log Viewer');
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->visit(route('portada'));
            $browser->assertRouteIs('portada');
            $browser->assertDontSee('Ignition');
        });
    }
}

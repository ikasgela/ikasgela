<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AllowedAppController;
use App\Http\Controllers\AllowedUrlController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileResourceController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FlashDeckController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntellijProjectController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LinkCollectionController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\MarkdownTextController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RubricController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\RuleGroupController;
use App\Http\Controllers\SafeExamController;
use App\Http\Controllers\SelectorController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\TinymceUploadController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YoutubeVideoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

// Localización
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeCookieRedirect', 'localizationRedirect']], function () {

    // Página principal
    Route::get('/', [HomeController::class, 'index'])
        ->name('portada');

    // Páginas públicas
    //Route::view('/documentacion', 'documentacion.index');

    // Gestión de usuarios
    Auth::routes(['verify' => true]);

    // Mostrar las imágenes incrustadas
    Route::get('/tinymce_url', [TinymceUploadController::class, 'getS3'])->name('tinymce.upload.url');

    // Safe exam browser
    Route::get('/safe_exam/{curso}/config_seb', [SafeExamController::class, 'config_seb'])
        ->withoutMiddleware('localeCookieRedirect')
        ->name('safe_exam.config_seb');

    // Cuenta bloqueada
    Route::view('/blocked', 'auth.blocked')
        ->name('blocked');

    // Sesión iniciada y cuenta verificada
    Route::middleware(['auth', 'verified'])->group(function () {

        // Perfil de usuario: activar/desactivar el tutorial
        Route::post('/users/toggle_help', [UserController::class, 'toggle_help'])
            ->name('users.toggle_help');

        // Perfil de usuario: editar y cambiar contraseña
        Route::get('/profile', [ProfileController::class, 'show'])
            ->name('profile.show');
        Route::get('/password', [ProfileController::class, 'password'])
            ->name('profile.password');
        Route::put('/profile/update_user', [ProfileController::class, 'updateUser'])
            ->name('profile.update.user');
        Route::put('/profile/update_password', [ProfileController::class, 'updatePassword'])
            ->name('profile.update.password');

        // Actualizar estado de una tarea
        Route::put('/actividades/{tarea}/estado', [ActividadController::class, 'actualizarEstado'])
            ->name('actividades.estado');

        // FileUpload
        Route::post('/upload_image', [FileController::class, 'imageUpload'])->name('files.upload.image');
        Route::post('/upload_document', [FileController::class, 'documentUpload'])->name('files.upload.document');
        Route::delete('/uploads/{file}', [FileController::class, 'postDelete'])->name('files.delete');
        Route::post('/files/{file}/rotate_left', [FileController::class, 'rotateLeft'])->name('files.rotate_left');
        Route::post('/files/{file}/rotate_right', [FileController::class, 'rotateRight'])->name('files.rotate_right');

        // Ajustes de notificaciones
        Route::get('/notifications', [NotificationController::class, 'edit'])
            ->name('notifications.edit');
        Route::put('/notifications', [NotificationController::class, 'update'])
            ->name('notifications.update');

        // Impersonar
        Route::impersonate();

        // Subir ficheros a TinyMCE
        Route::post('/tinymce_upload', [TinymceUploadController::class, 'uploadImage'])->name('tinymce.upload.image');

        // Descargar los repositorios propios
        Route::post('/intellij_projects/descargar', [IntellijProjectController::class, 'descargar_repos_usuario'])
            ->name('archivo.descargar');

        // Safe exam browser
        Route::get('/safe_exam/exit_seb/{quit_password_hash}', [SafeExamController::class, 'exit_seb'])
            ->withoutMiddleware('localeCookieRedirect')
            ->name('safe_exam.exit_seb');

        // Portada de todos los cursos
        Route::get('/portada', [AlumnoController::class, 'portada'])
            ->name('users.portada');
        Route::post('/portada', [AlumnoController::class, 'portada'])
            ->name('users.portada.filtro');

        // Alumno
        Route::middleware(['role:alumno'])->group(function () {

            // Mostrar el escritorio del alumno
            Route::get('/home', [AlumnoController::class, 'tareas'])
                ->name('users.home');

            // Descargar un repositorio
            Route::get('/intellij_projects/{intellij_project}/download', [IntellijProjectController::class, 'download'])
                ->withoutMiddleware('localeCookieRedirect')
                ->name('intellij_projects.download');
        });

        // Profesor
        Route::middleware(['role:profesor|admin'])->group(function () {

            // Panel de control
            Route::get('/alumnos', [ProfesorController::class, 'index'])
                ->name('profesor.index');

            // Selector de unidad
            Route::post('/alumnos', [ProfesorController::class, 'index'])
                ->name('profesor.index.filtro');

            Route::get('/alumnos/etiqueta', [ProfesorController::class, 'index'])
                ->name('profesor.index.etiqueta');

            // Asignar una tarea a un alumno
            Route::post('/alumnos/asignar_tareas', [ProfesorController::class, 'asignarTareasGrupo'])
                ->name('profesor.asignar_tareas_grupo');

            // Tareas actuales de un alumno
            Route::get('/alumnos/{user}/tareas', [ProfesorController::class, 'tareas'])
                ->name('profesor.tareas');

            // Selector de unidad
            Route::post('/alumnos/{user}/tareas', [ProfesorController::class, 'tareas'])
                ->name('profesor.tareas.filtro');

            // Asignar una tarea a un alumno
            Route::post('/alumnos/{user}/asignar_tarea', [ProfesorController::class, 'asignarTarea'])
                ->name('profesor.asignar_tarea');

            // Mostrar una tarea para revisar
            Route::get('/profesor/{user}/revisar/{tarea}', [ProfesorController::class, 'revisar'])
                ->name('profesor.revisar');

            // Ejecutar JPlag sobre una tarea
            Route::get('/profesor/jplag/{tarea}', [ProfesorController::class, 'jplag'])
                ->name('profesor.jplag');
            Route::get('/profesor/jplag_download/{tarea}', [ProfesorController::class, 'jplag_download'])
                ->withoutMiddleware('localeCookieRedirect')
                ->name('profesor.jplag_download');

            // Borrar una tarea
            Route::delete('/tareas/{user}/destroy/{tarea}', [TareaController::class, 'destroy'])
                ->name('tareas.destroy');

            // Borrar múltiples tareas
            Route::delete('/tareas/{user}/borrar_multiple', [TareaController::class, 'borrarMultiple'])
                ->name('tareas.borrar_multiple');

            // Editar una tarea
            Route::get('/tareas/{tarea}/edit', [TareaController::class, 'edit'])
                ->name('tareas.edit');
            Route::put('/tareas/{tarea}', [TareaController::class, 'update'])
                ->name('tareas.update');

            // Gestionar plantillas de actividades
            Route::get('/actividades/plantillas', [ActividadController::class, 'plantillas'])
                ->name('actividades.plantillas');

            // Gestionar plantillas de actividades - Selector de unidad
            Route::post('/actividades/plantillas', [ActividadController::class, 'plantillas'])
                ->name('actividades.plantillas.filtro');

            // Reordenar actividades
            Route::post('/actividades/reordenar/{a1}/{a2}', [ActividadController::class, 'reordenar'])
                ->name('actividades.reordenar');

            // Reordenar los recursos de una plantilla
            Route::post('/actividades/{actividad}/reordenar_recursos', [ActividadController::class, 'reordenar_recursos'])
                ->name('actividades.reordenar_recursos');

            // Modificar el número de columnas de un recurso
            Route::post('/actividades/{actividad}/recurso_modificar_columnas', [ActividadController::class, 'recurso_modificar_columnas'])
                ->name('actividades.recurso_modificar_columnas');

            // Reordenar unidades
            Route::post('/unidades/reordenar/{a1}/{a2}', [UnidadController::class, 'reordenar'])
                ->name('unidades.reordenar');

            // Reordenar preguntas e items
            Route::post('/preguntas/reordenar/{a1}/{a2}', [PreguntaController::class, 'reordenar'])
                ->name('preguntas.reordenar');
            Route::post('/items/reordenar/{a1}/{a2}', [ItemController::class, 'reordenar'])
                ->name('items.reordenar');

            // Reordenar feedback
            Route::post('/feedbacks/reordenar/{a1}/{a2}', [FeedbackController::class, 'reordenar'])
                ->name('feedbacks.reordenar');

            // Reordenar competencias de una cualificación
            Route::post('/qualifications/{qualification}/reordenar_skills', [QualificationController::class, 'reordenar_skills'])
                ->name('qualifications.reordenar_skills');

            // YoutubeVideo
            Route::resource('youtube_videos', YoutubeVideoController::class);
            Route::get('/youtube_videos/{actividad}/actividad', [YoutubeVideoController::class, 'actividad'])
                ->name('youtube_videos.actividad');
            Route::post('/youtube_videos/{actividad}/asociar', [YoutubeVideoController::class, 'asociar'])
                ->name('youtube_videos.asociar');
            Route::delete('/youtube_videos/{actividad}/desasociar/{youtube_video}', [YoutubeVideoController::class, 'desasociar'])
                ->name('youtube_videos.desasociar');
            Route::post('/youtube_videos/{actividad}/toggle_titulo_visible/{youtube_video}', [YoutubeVideoController::class, 'toggle_titulo_visible'])
                ->name('youtube_videos.toggle.titulo_visible');
            Route::post('/youtube_videos/{actividad}/toggle_descripcion_visible/{youtube_video}', [YoutubeVideoController::class, 'toggle_descripcion_visible'])
                ->name('youtube_videos.toggle.descripcion_visible');
            Route::post('/youtube_videos/{youtube_video}/duplicar', [YoutubeVideoController::class, 'duplicar'])
                ->name('youtube_videos.duplicar');

            // Clonador de IntellijProject
            Route::get('/intellij_projects/copia', [IntellijProjectController::class, 'copia'])
                ->name('intellij_projects.copia');
            Route::post('/intellij_projects/clonar', [IntellijProjectController::class, 'clonar'])
                ->name('intellij_projects.clonar');
            Route::delete('/intellij_projects/borrar/{id}', [IntellijProjectController::class, 'borrar'])
                ->name('intellij_projects.borrar');

            // Descargar proyectos de IntellijProject
            Route::get('/intellij_projects/descargar', [IntellijProjectController::class, 'descargar_repos'])
                ->name('intellij_projects.descargar');
            Route::post('/intellij_projects/descargar_repos', [IntellijProjectController::class, 'descargar_repos'])
                ->name('intellij_projects.descargar.repos');
            Route::post('/intellij_projects/descargar_plantillas', [IntellijProjectController::class, 'descargar_plantillas'])
                ->name('intellij_projects.descargar.plantillas');
            Route::post('/intellij_projects/descargar_plantillas_curso', [IntellijProjectController::class, 'descargar_plantillas_curso'])
                ->name('intellij_projects.descargar.plantillas.curso');

            // IntellijProject
            Route::resource('intellij_projects', IntellijProjectController::class);
            Route::get('/intellij_projects/{actividad}/actividad', [IntellijProjectController::class, 'actividad'])
                ->name('intellij_projects.actividad');
            Route::post('/intellij_projects/{actividad}/asociar', [IntellijProjectController::class, 'asociar'])
                ->name('intellij_projects.asociar');
            Route::delete('/intellij_projects/{actividad}/desasociar/{intellij_project}', [IntellijProjectController::class, 'desasociar'])
                ->name('intellij_projects.desasociar');
            Route::post('/intellij_projects/{actividad}/toggle_titulo_visible/{intellij_project}', [IntellijProjectController::class, 'toggle_titulo_visible'])
                ->name('intellij_projects.toggle.titulo_visible');
            Route::post('/intellij_projects/{actividad}/toggle_descripcion_visible/{intellij_project}', [IntellijProjectController::class, 'toggle_descripcion_visible'])
                ->name('intellij_projects.toggle.descripcion_visible');
            Route::post('/intellij_projects/{intellij_project}/duplicar', [IntellijProjectController::class, 'duplicar'])
                ->name('intellij_projects.duplicar');
            Route::post('/intellij_projects/{actividad}/toggle_incluir_siempre/{intellij_project}', [IntellijProjectController::class, 'toggle_incluir_siempre'])
                ->name('intellij_projects.toggle.incluir_siempre');

            // Bloquear y desbloquear repositorios
            Route::post('/intellij_projects/{intellij_project}/{actividad}/lock', [IntellijProjectController::class, 'lock'])
                ->name('intellij_projects.lock');
            Route::post('/intellij_projects/{intellij_project}/{actividad}/unlock', [IntellijProjectController::class, 'unlock'])
                ->name('intellij_projects.unlock');

            // Editar un fork ya hecho
            Route::get('/intellij_projects/{intellij_project}/{actividad}/edit_fork', [IntellijProjectController::class, 'edit_fork'])
                ->name('intellij_projects.edit_fork');
            Route::put('/intellij_projects/{intellij_project}/{actividad}/update_fork', [IntellijProjectController::class, 'update_fork'])
                ->name('intellij_projects.update_fork');

            // MarkdownText
            Route::resource('markdown_texts', MarkdownTextController::class);
            Route::get('/markdown_texts/{actividad}/actividad', [MarkdownTextController::class, 'actividad'])
                ->name('markdown_texts.actividad');
            Route::post('/markdown_texts/{actividad}/asociar', [MarkdownTextController::class, 'asociar'])
                ->name('markdown_texts.asociar');
            Route::delete('/markdown_texts/{actividad}/desasociar/{markdown_text}', [MarkdownTextController::class, 'desasociar'])
                ->name('markdown_texts.desasociar');
            Route::post('/markdown_texts/{markdown_text}/duplicar', [MarkdownTextController::class, 'duplicar'])
                ->name('markdown_texts.duplicar');
            Route::get('/markdown_texts/{markdown_text}/borrar_cache', [MarkdownTextController::class, 'borrar_cache'])
                ->name('markdown_texts.borrar_cache');

            // Cuestionario
            Route::resource('cuestionarios', CuestionarioController::class);
            Route::get('/cuestionarios/{actividad}/actividad', [CuestionarioController::class, 'actividad'])
                ->name('cuestionarios.actividad');
            Route::post('/cuestionarios/{actividad}/asociar', [CuestionarioController::class, 'asociar'])
                ->name('cuestionarios.asociar');
            Route::delete('/cuestionarios/{actividad}/desasociar/{cuestionario}', [CuestionarioController::class, 'desasociar'])
                ->name('cuestionarios.desasociar');

            Route::resource('preguntas', PreguntaController::class);
            Route::get('/preguntas/{cuestionario}/anyadir', [PreguntaController::class, 'anyadir'])
                ->name('preguntas.anyadir');
            Route::resource('items', ItemController::class);
            Route::get('/items/{pregunta}/anyadir', [ItemController::class, 'anyadir'])
                ->name('items.anyadir');

            Route::post('/cuestionarios/{cuestionario}/duplicar', [CuestionarioController::class, 'duplicar'])
                ->name('cuestionarios.duplicar');
            Route::post('/preguntas/{pregunta}/duplicar', [PreguntaController::class, 'duplicar'])
                ->name('preguntas.duplicar');
            Route::post('/items/{item}/duplicar', [ItemController::class, 'duplicar'])
                ->name('items.duplicar');

            // FileUpload
            Route::resource('file_uploads', FileUploadController::class);
            Route::get('/file_uploads/{actividad}/actividad', [FileUploadController::class, 'actividad'])
                ->name('file_uploads.actividad');
            Route::post('/file_uploads/{actividad}/asociar', [FileUploadController::class, 'asociar'])
                ->name('file_uploads.asociar');
            Route::delete('/file_uploads/{actividad}/desasociar/{file_upload}', [FileUploadController::class, 'desasociar'])
                ->name('file_uploads.desasociar');
            Route::post('/file_uploads/{actividad}/toggle_titulo_visible/{file_upload}', [FileUploadController::class, 'toggle_titulo_visible'])
                ->name('file_uploads.toggle.titulo_visible');
            Route::post('/file_uploads/{actividad}/toggle_descripcion_visible/{file_upload}', [FileUploadController::class, 'toggle_descripcion_visible'])
                ->name('file_uploads.toggle.descripcion_visible');
            Route::post('/file_uploads/{file_upload}/duplicar', [FileUploadController::class, 'duplicar'])
                ->name('file_uploads.duplicar');

            // FileResource
            Route::resource('file_resources', FileResourceController::class);
            Route::get('/file_resources/{actividad}/actividad', [FileResourceController::class, 'actividad'])
                ->name('file_resources.actividad');
            Route::post('/file_resources/{actividad}/asociar', [FileResourceController::class, 'asociar'])
                ->name('file_resources.asociar');
            Route::delete('/file_resources/{actividad}/desasociar/{file_resource}', [FileResourceController::class, 'desasociar'])
                ->name('file_resources.desasociar');
            Route::post('/files/reordenar/{a1}/{a2}', [FileController::class, 'reordenar'])
                ->name('files.reordenar');
            Route::post('/file_resources/{actividad}/toggle_titulo_visible/{file_resource}', [FileResourceController::class, 'toggle_titulo_visible'])
                ->name('file_resources.toggle.titulo_visible');
            Route::post('/file_resources/{actividad}/toggle_descripcion_visible/{file_resource}', [FileResourceController::class, 'toggle_descripcion_visible'])
                ->name('file_resources.toggle.descripcion_visible');
            Route::post('/file_resources/{file_resource}/duplicar', [FileResourceController::class, 'duplicar'])
                ->name('file_resources.duplicar');
            Route::post('/files/{file}/toggle_visible', [FileController::class, 'toggle_visible'])
                ->name('files.toggle.visible');

            // LinkCollection
            Route::resource('link_collections', LinkCollectionController::class);
            Route::get('/link_collections/{actividad}/actividad', [LinkCollectionController::class, 'actividad'])
                ->name('link_collections.actividad');
            Route::post('/link_collections/{actividad}/asociar', [LinkCollectionController::class, 'asociar'])
                ->name('link_collections.asociar');
            Route::delete('/link_collections/{actividad}/desasociar/{link_collection}', [LinkCollectionController::class, 'desasociar'])
                ->name('link_collections.desasociar');
            Route::post('/links', [LinkController::class, 'store'])->name('links.store');
            Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
            Route::post('/links/reordenar/{a1}/{a2}', [LinkController::class, 'reordenar'])
                ->name('links.reordenar');
            Route::post('/link_collections/{actividad}/toggle_titulo_visible/{link_collection}', [LinkCollectionController::class, 'toggle_titulo_visible'])
                ->name('link_collections.toggle.titulo_visible');
            Route::post('/link_collections/{actividad}/toggle_descripcion_visible/{link_collection}', [LinkCollectionController::class, 'toggle_descripcion_visible'])
                ->name('link_collections.toggle.descripcion_visible');
            Route::post('/link_collections/{link_collection}/duplicar', [LinkCollectionController::class, 'duplicar'])
                ->name('link_collections.duplicar');

            // Modificar la nota manualmente
            Route::get('/profesor/{user}/{curso}/nota_manual', [ProfesorController::class, 'editNotaManual'])
                ->name('profesor.nota_manual.edit');
            Route::post('/profesor/{user}/{curso}/nota_manual', [ProfesorController::class, 'updateNotaManual'])
                ->name('profesor.nota_manual.update');

            // Asignar tareas a equipos
            Route::post('/teams/filtro', [TeamController::class, 'index'])
                ->name('teams.index.filtro');
            Route::post('/teams/{team}/show', [TeamController::class, 'show'])
                ->name('teams.show.filtro');
            Route::post('/profesor/asignar_tareas_equipo', [ProfesorController::class, 'asignarTareasEquipo'])
                ->name('profesor.asignar_tareas_equipo');
            Route::post('/profesor/{team}/asignar_tarea_equipo', [ProfesorController::class, 'asignarTareaEquipo'])
                ->name('profesor.asignar_tarea_equipo');

            // Selector
            Route::resource('selectors', SelectorController::class);
            Route::get('/selectors/{actividad}/actividad', [SelectorController::class, 'actividad'])
                ->name('selectors.actividad');
            Route::post('/selectors/{actividad}/asociar', [SelectorController::class, 'asociar'])
                ->name('selectors.asociar');
            Route::delete('/selectors/{actividad}/desasociar/{selector}', [SelectorController::class, 'desasociar'])
                ->name('selectors.desasociar');
            Route::resource('rule_groups', RuleGroupController::class);
            Route::get('/rule_groups/{selector}/anyadir', [RuleGroupController::class, 'create'])
                ->name('rule_groups.anyadir');
            Route::resource('rules', RuleController::class);
            Route::get('/rules/{rule_group}/anyadir', [RuleController::class, 'create'])
                ->name('rules.anyadir');
            Route::post('/selectors/{selector}/duplicar', [SelectorController::class, 'duplicar'])
                ->name('selectors.duplicar');
            Route::post('/rule_groups/{rule_group}/duplicar', [RuleGroupController::class, 'duplicar'])
                ->name('rule_groups.duplicar');
            Route::post('/rules/{rule}/duplicar', [RuleController::class, 'duplicar'])
                ->name('rules.duplicar');

            // Rúbricas
            Route::resource('rubrics', RubricController::class);
            Route::get('/rubrics/{actividad}/actividad', [RubricController::class, 'actividad'])
                ->name('rubrics.actividad');
            Route::post('/rubrics/{actividad}/asociar', [RubricController::class, 'asociar'])
                ->name('rubrics.asociar');
            Route::delete('/rubrics/{actividad}/desasociar/{rubric}', [RubricController::class, 'desasociar'])
                ->name('rubrics.desasociar');
            Route::post('/rubrics/{rubric}/duplicar', [RubricController::class, 'duplicar'])
                ->name('rubrics.duplicar');

            // Activar/desactivar la matrícula en los cursos
            Route::post('/cursos/{curso}/toggle_matricula_abierta', [CursoController::class, 'toggle_matricula_abierta'])
                ->name('cursos.toggle.matricula_abierta');

            // Activar/desactivar el registro en las organizaciones
            Route::post('/organizations/{organization}/toggle_registration_open', [OrganizationController::class, 'toggle_registration_open'])
                ->name('organizations.toggle.registration_open');

            // Rúbricas
            Route::resource('flash_decks', FlashDeckController::class);
            Route::get('/flash_decks/{actividad}/actividad', [FlashDeckController::class, 'actividad'])
                ->name('flash_decks.actividad');
            Route::post('/flash_decks/{actividad}/asociar', [FlashDeckController::class, 'asociar'])
                ->name('flash_decks.asociar');
            Route::delete('/flash_decks/{actividad}/desasociar/{flash_deck}', [FlashDeckController::class, 'desasociar'])
                ->name('flash_decks.desasociar');
            Route::post('/flash_decks/{flash_deck}/duplicar', [FlashDeckController::class, 'duplicar'])
                ->name('flash_decks.duplicar');

            // Resultados de test
            Route::resource('test_results', TestResultController::class);
            Route::get('/test_results/{actividad}/actividad', [TestResultController::class, 'actividad'])
                ->name('test_results.actividad');
            Route::post('/test_results/{actividad}/asociar', [TestResultController::class, 'asociar'])
                ->name('test_results.asociar');
            Route::delete('/test_results/{actividad}/desasociar/{test_result}', [TestResultController::class, 'desasociar'])
                ->name('test_results.desasociar');
            Route::post('/test_results/{test_result}/duplicar', [TestResultController::class, 'duplicar'])
                ->name('test_results.duplicar');
            Route::post('/test_results/{actividad}/toggle_titulo_visible/{test_result}', [TestResultController::class, 'toggle_titulo_visible'])
                ->name('test_results.toggle.titulo_visible');
            Route::post('/test_results/{actividad}/toggle_descripcion_visible/{test_result}', [TestResultController::class, 'toggle_descripcion_visible'])
                ->name('test_results.toggle.descripcion_visible');
            Route::put('/test_results/{test_result}/rellenar', [TestResultController::class, 'rellenar'])
                ->name('test_results.rellenar');
        });

        // Administrador
        Route::middleware(['role:admin'])->group(function () {

            // Portada del admin
            Route::view('/admin', 'welcome2')
                ->name('admin.index');

            // Lista de usuarios
            Route::get('/users', [UserController::class, 'index'])
                ->name('users.index');
            Route::match(['GET', 'POST'], '/users/filtro', [UserController::class, 'index'])
                ->name('users.index.filtro');
            Route::post('/users/acciones_grupo', [UserController::class, 'acciones_grupo'])
                ->name('users.acciones_grupo');

            // Crear un usuario manualmente
            Route::get('/users/create', [UserController::class, 'create'])
                ->name('users.create');
            Route::post('/users', [UserController::class, 'store'])
                ->name('users.store');

            // Editar usuario
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])
                ->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])
                ->name('users.update');
            Route::get('/users/{user}/password', [UserController::class, 'password'])
                ->name('users.password');
            Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])
                ->name('users.update.password');

            // Borrar un usuario
            Route::delete('/users/{user}', [UserController::class, 'destroy'])
                ->name('users.destroy');

            // Roles
            Route::resource('roles', RoleController::class);

            // Estructura del curso
            Route::resource('cursos', CursoController::class);
            Route::resource('unidades', UnidadController::class)
                ->parameters(['unidades' => 'unidad']);
            Route::post('/actividades/{actividad}/duplicar', [ActividadController::class, 'duplicar'])
                ->name('actividades.duplicar');
            Route::post('/actividades/duplicar_grupo', [ActividadController::class, 'duplicar_grupo'])
                ->name('actividades.duplicar_grupo');
            Route::resource('actividades', ActividadController::class)
                ->parameters(['actividades' => 'actividad']);

            // CRUD - Organizaciones
            Route::resource('organizations', OrganizationController::class);

            // CRUD - Periodos
            Route::resource('periods', PeriodController::class);

            // CRUD - Categorías
            Route::resource('categories', CategoryController::class);

            // CRUD - Grupos
            Route::resource('groups', GroupController::class);

            // Borrar entradas del registro
            Route::delete('registros/{registro}', [RegistroController::class, 'destroy'])
                ->name('registros.destroy');

            // CRUD - Cualificaciones
            Route::resource('qualifications', QualificationController::class);
            Route::post('/qualifications/filtro', [QualificationController::class, 'index'])
                ->name('qualifications.index.filtro');
            Route::post('/qualifications/{qualification}/duplicar', [QualificationController::class, 'duplicar'])
                ->name('qualifications.duplicar');

            // CRUD - Competencias
            Route::resource('skills', SkillController::class);
            Route::post('/skills/filtro', [SkillController::class, 'index'])
                ->name('skills.index.filtro');

            // Filtros por curso
            Route::post('/unidades/filtro', [UnidadController::class, 'index'])
                ->name('unidades.index.filtro');
            Route::post('/actividades/filtro', [ActividadController::class, 'index'])
                ->name('actividades.index.filtro');
            Route::post('/intellij_projects/filtro', [IntellijProjectController::class, 'index'])
                ->name('intellij_projects.index.filtro');
            Route::post('/markdown_texts/filtro', [MarkdownTextController::class, 'index'])
                ->name('markdown_texts.index.filtro');
            Route::post('/youtube_videos/filtro', [YoutubeVideoController::class, 'index'])
                ->name('youtube_videos.index.filtro');
            Route::post('/file_resources/filtro', [FileResourceController::class, 'index'])
                ->name('file_resources.index.filtro');
            Route::post('/file_uploads/filtro', [FileUploadController::class, 'index'])
                ->name('file_uploads.index.filtro');
            Route::post('/cuestionarios/filtro', [CuestionarioController::class, 'index'])
                ->name('cuestionarios.index.filtro');
            Route::post('/link_collections/filtro', [LinkCollectionController::class, 'index'])
                ->name('link_collections.index.filtro');
            Route::post('/selectors/filtro', [SelectorController::class, 'index'])
                ->name('selectors.index.filtro');
            Route::post('/rubrics/filtro', [RubricController::class, 'index'])
                ->name('rubrics.index.filtro');
            Route::post('/flash_decks/filtro', [FlashDeckController::class, 'index'])
                ->name('flash_decks.index.filtro');
            Route::post('/test_results/filtro', [TestResultController::class, 'index'])
                ->name('test_results.index.filtro');

            // CRUD - Feedbacks
            Route::resource('feedbacks', FeedbackController::class);
            Route::post('/feedback_mensaje', [FeedbackController::class, 'save'])
                ->name('feedbacks.save');
            Route::get('/feedbacks/{actividad}/create_actividad', [FeedbackController::class, 'create_actividad'])
                ->name('feedbacks.create_actividad');

            // CRUD - Milestones
            Route::resource('milestones', MilestoneController::class);
            Route::post('/milestones/filtro', [MilestoneController::class, 'index'])
                ->name('milestones.index.filtro');

            // Visor de logs: https://github.com/rap2hpoutre/laravel-log-viewer
            Route::get('logs', [LogViewerController::class, 'index'])
                ->name('logs');

            // Ver entradas en el registro
            Route::get('/registros', [RegistroController::class, 'index'])
                ->name('registros.index');

            // Filtrar el registro por alumno
            Route::get('/registros_alumno', [RegistroController::class, 'index'])
                ->name('registros_alumno.index');
            Route::post('/registros_alumno', [RegistroController::class, 'index'])
                ->name('registros_alumno.alumno');

            // Probar notificaciones
            Route::get('/notifications/test', [NotificationController::class, 'test'])
                ->name('notifications.test');

            // Activación manual de usuario
            Route::post('/user_manual_activation', [UserController::class, 'manualActivation'])
                ->name('users.manual_activation');

            // Bloqueo/desbloqueo del usuario
            Route::post('/user_toggle_blocked', [UserController::class, 'toggleBlocked'])
                ->name('users.toggle_blocked');

            // Informe de todas las actividades del curso
            Route::get('/actividades_export', [ActividadController::class, 'export'])
                ->withoutMiddleware('localeCookieRedirect')
                ->name('actividades.export');

            // Exportar/importar cursos
            Route::post('/cursos/{curso}/export', [CursoController::class, 'export'])
                ->name('cursos.export');
            Route::post('/cursos.import', [CursoController::class, 'import'])
                ->name('cursos.import');

            // Reiniciar los contenidos de un curso
            Route::delete('/cursos/{curso}/reset', [CursoController::class, 'reset'])
                ->name('cursos.reset');

            // Safe Exam Browser
            Route::get('/safe_exam', [SafeExamController::class, 'index'])
                ->name('safe_exam.index');
            Route::post('/safe_exam/{curso}/reset_token', [SafeExamController::class, 'reset_token'])
                ->name('safe_exam.reset_token');
            Route::post('/safe_exam/{curso}/reset_quit_password', [SafeExamController::class, 'reset_quit_password'])
                ->name('safe_exam.reset_quit_password');
            Route::delete('/safe_exam/{curso}/delete_token', [SafeExamController::class, 'delete_token'])
                ->name('safe_exam.delete_token');
            Route::delete('/safe_exam/{curso}/delete_quit_password', [SafeExamController::class, 'delete_quit_password'])
                ->name('safe_exam.delete_quit_password');

            Route::get('/safe_exam/{safe_exam}/allowed', [SafeExamController::class, 'allowed'])
                ->name('safe_exam.allowed');

            Route::get('/allowed_apps/{safe_exam}/create', [AllowedAppController::class, 'create'])
                ->name('allowed_apps.create');
            Route::post('/allowed_apps', [AllowedAppController::class, 'store'])
                ->name('allowed_apps.store');
            Route::get('/allowed_apps/{allowed_app}/edit', [AllowedAppController::class, 'edit'])
                ->name('allowed_apps.edit');
            Route::put('/allowed_apps/{allowed_app}', [AllowedAppController::class, 'update'])
                ->name('allowed_apps.update');
            Route::delete('/allowed_apps/{allowed_app}', [AllowedAppController::class, 'destroy'])
                ->name('allowed_apps.destroy');
            Route::post('/allowed_apps/{allowed_app}/duplicate', [AllowedAppController::class, 'duplicate'])
                ->name('allowed_apps.duplicate');

            Route::get('/allowed_urls/{safe_exam}/create', [AllowedUrlController::class, 'create'])
                ->name('allowed_urls.create');
            Route::post('/allowed_urls', [AllowedUrlController::class, 'store'])
                ->name('allowed_urls.store');
            Route::get('/allowed_urls/{allowed_url}/edit', [AllowedUrlController::class, 'edit'])
                ->name('allowed_urls.edit');
            Route::put('/allowed_urls/{allowed_url}', [AllowedUrlController::class, 'update'])
                ->name('allowed_urls.update');
            Route::delete('/allowed_urls/{allowed_url}', [AllowedUrlController::class, 'destroy'])
                ->name('allowed_urls.destroy');
            Route::post('/allowed_urls/{allowed_url}/duplicate', [AllowedUrlController::class, 'duplicate'])
                ->name('allowed_urls.duplicate');
        });

        // Alumnos y profesores
        Route::middleware(['role:alumno|profesor'])->group(function () {

            // Crear entradas en el registro
            Route::post('/registros', [RegistroController::class, 'store'])
                ->name('registros.store');

            // Responder a cuestionarios
            Route::put('/cuestionarios/{cuestionario}/respuesta', [CuestionarioController::class, 'respuesta'])
                ->name('cuestionarios.respuesta');

            // Previsualizar una actividad
            Route::get('/actividades/{actividad}/preview', [ActividadController::class, 'preview'])
                ->name('actividades.preview');
        });

        // Profesor y tutor
        Route::middleware(['role:profesor|tutor'])->group(function () {

            // Informe de grupo
            Route::get('/tutor', [TutorController::class, 'index'])
                ->name('tutor.index');
            Route::post('/tutor', [TutorController::class, 'index'])
                ->name('tutor.index.filtro');
            Route::get('/tutor/export', [TutorController::class, 'export'])
                ->withoutMiddleware('localeCookieRedirect')
                ->name('tutor.export');
            Route::get('/tutor/tareas_enviadas', [TutorController::class, 'tareas_enviadas'])
                ->name('tutor.tareas_enviadas');

            // Ver resultados de otros alumnos
            Route::post('/results', [ResultController::class, 'index'])
                ->name('results.alumno');

            // Resultados de otro usuario en PDF
            Route::post('/results/pdf', [ResultController::class, 'pdf'])
                ->name('results.pdf.filtro');

            // Ver el progreso de otros alumnos
            Route::post('/outline', [ArchivoController::class, 'outline'])
                ->name('archivo.outline.filtro');

            // Ver archivo de otros alumnos
            Route::post('/archivo', [ArchivoController::class, 'index'])
                ->name('archivo.alumno');

            // Borrar los datos en caché del usuario
            Route::post('/users/{user}/limpiar_cache', [UserController::class, 'limpiar_cache'])
                ->name('users.limpiar_cache');
            Route::post('/cursos/{curso}/limpiar_cache', [CursoController::class, 'limpiar_cache'])
                ->name('cursos.limpiar_cache');

            // Ver diario de actividades
            Route::get('/diario', [ArchivoController::class, 'diario'])
                ->name('archivo.diario');
            Route::post('/diario', [ArchivoController::class, 'diario'])
                ->name('archivo.diario.usuario');

            // Ampliar el plazo de todas las actividades de un curso
            Route::post('/actividades/{curso}/ampliar_todas', [ActividadController::class, 'ampliar_plazo_todas'])
                ->name('actividades.ampliar_todas');
        });

        // Alumno, profesor y tutor
        Route::middleware(['role:alumno|profesor|tutor'])->group(function () {

            // Resultados propios
            Route::get('/results', [ResultController::class, 'index'])
                ->name('results.index');

            // Resultados propios en PDF
            Route::get('/results/pdf', [ResultController::class, 'pdf'])
                ->name('results.pdf');

            // Resultados de otras evaluaciones
            Route::get('/results/milestone', [ResultController::class, 'index'])
                ->name('results.milestone.index');
            Route::post('/results/milestone', [ResultController::class, 'index'])
                ->name('results.milestone');

            // Archivo propio
            Route::get('/archivo/{actividad}', [ArchivoController::class, 'show'])
                ->name('archivo.show');
            Route::get('/archivo', [ArchivoController::class, 'index'])
                ->name('archivo.index');

            // Esquema del curso
            Route::get('/outline', [ArchivoController::class, 'outline'])
                ->name('archivo.outline');

            // Matricularse en un curso
            Route::post('/cursos/{curso}/{user}/matricular', [CursoController::class, 'matricular'])
                ->name('cursos.matricular');
            Route::post('/cursos/{curso}/{user}/curso_actual', [CursoController::class, 'curso_actual'])
                ->name('cursos.curso_actual');
        });

        // Profesor y administrador
        Route::middleware(['role:profesor|admin'])->group(function () {

            // CRUD - Equipos
            Route::resource('teams', TeamController::class);
        });

        // Mensajes
        Route::group(['prefix' => 'messages'], function () {
            Route::get('/', [MessagesController::class, 'index'])
                ->name('messages');
            Route::get('create', [MessagesController::class, 'create'])
                ->name('messages.create');
            Route::post('create', [MessagesController::class, 'create'])
                ->name('messages.create-with-subject');
            Route::post('create_team', [MessagesController::class, 'create_team'])
                ->name('messages.create-with-subject-team');
            Route::post('/', [MessagesController::class, 'store'])
                ->name('messages.store');
            Route::get('{id}', [MessagesController::class, 'show'])
                ->name('messages.show');
            Route::put('{id}', [MessagesController::class, 'update'])
                ->name('messages.update');
            Route::delete('/delete_thread/{id}', [MessagesController::class, 'destroy'])
                ->name('messages.destroy');
            Route::delete('/delete_message/{id}', [MessagesController::class, 'destroyMessage'])
                ->name('messages.destroy_message');
        });

        // Pruebas
        if (config('app.debug')) {
            Route::get('/files', [FileController::class, 'getFiles'])->name('files');
        }
    });
});

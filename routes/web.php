<?php

// Localización
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeCookieRedirect', 'localizationRedirect']], function () {

    // Página principal
    Route::get('/', 'HomeController@index')
        ->name('portada');

    // Páginas públicas
    //Route::view('/documentacion', 'documentacion.index');

    // Gestión de usuarios
    Auth::routes(['verify' => true]);

    # Honey
    Route::post('login', 'Auth\LoginController@login')->middleware(['honey']);
    Route::post('register', 'Auth\RegisterController@register')->middleware(['honey']);
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->middleware(['honey'])->name('password.email');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->middleware(['honey'])->name('password.update');

    // Control de la barra lateral
    Route::post('/settings/api', 'SettingController@api')
        ->name('settings.api');

    // Sesión iniciada y cuenta verificada
    Route::middleware(['auth', 'verified'])->group(function () {

        // Perfil de usuario: activar/desactivar el tutorial
        Route::post('/users/toggle_help', 'UserController@toggle_help')
            ->name('users.toggle_help');

        // Perfil de usuario: editar y cambiar contraseña
        Route::get('/profile', 'ProfileController@show')
            ->name('profile.show');
        Route::get('/password', 'ProfileController@password')
            ->name('profile.password');
        Route::put('/profile/update_user', 'ProfileController@updateUser')
            ->name('profile.update.user');
        Route::put('/profile/update_password', 'ProfileController@updatePassword')
            ->name('profile.update.password');

        // Actualizar estado de una tarea
        Route::put('/actividades/{tarea}/estado', 'ActividadController@actualizarEstado')
            ->name('actividades.estado');

        // Guardar ajustes
        Route::get('/settings', 'SettingController@editar')
            ->name('settings.editar');
        Route::post('/settings', 'SettingController@guardar')
            ->name('settings.guardar');

        // FileUpload
        Route::post('/upload_image', 'FileController@imageUpload')->name('files.upload.image');
        Route::post('/upload_document', 'FileController@documentUpload')->name('files.upload.document');
        Route::delete('/uploads/{file}', 'FileController@postDelete')->name('files.delete');
        Route::post('/files/{file}/rotate_left', 'FileController@rotateLeft')->name('files.rotate_left');
        Route::post('/files/{file}/rotate_right', 'FileController@rotateRight')->name('files.rotate_right');

        // Ajustes de notificaciones
        Route::get('/notifications', 'NotificationController@edit')
            ->name('notifications.edit');
        Route::put('/notifications', 'NotificationController@update')
            ->name('notifications.update');

        // Impersonar
        Route::impersonate();

        // Subir ficheros a TinyMCE
        Route::post('/tinymce_upload', 'TinymceUploadController@uploadImage')->name('tinymce.upload.image');
        Route::get('/tinymce_url', 'TinymceUploadController@getS3')->name('tinymce.upload.url');

        // Alumno
        Route::middleware(['role:alumno'])->group(function () {

            // Mostrar el escritorio del alumno
            Route::get('/home', 'AlumnoController@tareas')
                ->name('users.home');
            Route::get('/portada', 'AlumnoController@portada')
                ->name('users.portada');

            // Fork de un proyecto de Intellij
            Route::get('/intellij_projects/{actividad}/fork/{intellij_project}', 'IntellijProjectController@fork')
                ->name('intellij_projects.fork');
            Route::get('/intellij_projects/status/{actividad}/fork/{intellij_project}', 'IntellijProjectController@is_forking')
                ->name('intellij_projects.is_forking');

            // Descargar un repositorio
            Route::get('/intellij_projects/{intellij_project}/download', 'IntellijProjectController@download')
                ->withoutMiddleware('localeCookieRedirect')
                ->name('intellij_projects.download');

            // Matricularse en un curso
            Route::post('/cursos/{curso}/{user}/matricular', 'CursoController@matricular')
                ->name('cursos.matricular');
            Route::post('/cursos/{curso}/{user}/curso_actual', 'CursoController@curso_actual')
                ->name('cursos.curso_actual');
        });

        // Profesor
        Route::middleware(['role:profesor'])->group(function () {

            // Panel de control
            Route::get('/alumnos', 'ProfesorController@index')
                ->name('profesor.index');

            // Selector de unidad
            Route::post('/alumnos', 'ProfesorController@index')
                ->name('profesor.index.filtro');

            Route::get('/alumnos/etiqueta', 'ProfesorController@index')
                ->name('profesor.index.etiqueta');

            // Asignar una tarea a un alumno
            Route::post('/alumnos/asignar_tareas', 'ProfesorController@asignarTareasGrupo')
                ->name('profesor.asignar_tareas_grupo');

            // Tareas actuales de un alumno
            Route::get('/alumnos/{user}/tareas', 'ProfesorController@tareas')
                ->name('profesor.tareas');

            // Selector de unidad
            Route::post('/alumnos/{user}/tareas', 'ProfesorController@tareas')
                ->name('profesor.tareas.filtro');

            // Asignar una tarea a un alumno
            Route::post('/alumnos/{user}/asignar_tarea', 'ProfesorController@asignarTarea')
                ->name('profesor.asignar_tarea');

            // Mostrar una tarea para revisar
            Route::get('/profesor/{user}/revisar/{tarea}', 'ProfesorController@revisar')
                ->name('profesor.revisar');

            // Ejecutar JPlag sobre una tarea
            Route::get('/profesor/jplag/{tarea}', 'ProfesorController@jplag')
                ->name('profesor.jplag');
            Route::get('/profesor/jplag_download/{tarea}', 'ProfesorController@jplag_download')
                ->withoutMiddleware('localeCookieRedirect')
                ->name('profesor.jplag_download');

            // Borrar una tarea
            Route::delete('/tareas/{user}/destroy/{tarea}', 'TareaController@destroy')
                ->name('tareas.destroy');

            // Borrar múltiples tareas
            Route::post('/tareas/{user}/borrar_multiple', 'TareaController@borrarMultiple')
                ->name('tareas.borrar_multiple');

            // Editar una tarea
            Route::get('/tareas/{tarea}/edit', 'TareaController@edit')
                ->name('tareas.edit');
            Route::put('/tareas/{tarea}', 'TareaController@update')
                ->name('tareas.update');

            // Gestionar plantillas de actividades
            Route::get('/actividades/plantillas', 'ActividadController@plantillas')
                ->name('actividades.plantillas');

            // Gestionar plantillas de actividades - Selector de unidad
            Route::post('/actividades/plantillas', 'ActividadController@plantillas')
                ->name('actividades.plantillas.filtro');

            // Reordenar actividades
            Route::post('/actividades/reordenar/{a1}/{a2}', 'ActividadController@reordenar')
                ->name('actividades.reordenar');

            // Reordenar los recursos de una plantilla
            Route::post('/actividades/{actividad}/reordenar_recursos', 'ActividadController@reordenar_recursos')
                ->name('actividades.reordenar_recursos');

            // Modificar el número de columnas de un recurso
            Route::post('/actividades/{actividad}/recurso_modificar_columnas', 'ActividadController@recurso_modificar_columnas')
                ->name('actividades.recurso_modificar_columnas');

            // Reordenar unidades
            Route::post('/unidades/reordenar/{a1}/{a2}', 'UnidadController@reordenar')
                ->name('unidades.reordenar');

            // Reordenar preguntas e items
            Route::post('/preguntas/reordenar/{a1}/{a2}', 'PreguntaController@reordenar')
                ->name('preguntas.reordenar');
            Route::post('/items/reordenar/{a1}/{a2}', 'ItemController@reordenar')
                ->name('items.reordenar');

            // Reordenar feedback
            Route::post('/feedbacks/reordenar/{a1}/{a2}', 'FeedbackController@reordenar')
                ->name('feedbacks.reordenar');

            // Reordenar competencias de una cualificación
            Route::post('/qualifications/{qualification}/reordenar_skills', 'QualificationController@reordenar_skills')
                ->name('qualifications.reordenar_skills');

            // YoutubeVideo
            Route::resource('youtube_videos', 'YoutubeVideoController');
            Route::get('/youtube_videos/{actividad}/actividad', 'YoutubeVideoController@actividad')
                ->name('youtube_videos.actividad');
            Route::post('/youtube_videos/{actividad}/asociar', 'YoutubeVideoController@asociar')
                ->name('youtube_videos.asociar');
            Route::delete('/youtube_videos/{actividad}/desasociar/{youtube_video}', 'YoutubeVideoController@desasociar')
                ->name('youtube_videos.desasociar');
            Route::post('/youtube_videos/{actividad}/toggle_titulo_visible/{youtube_video}', 'YoutubeVideoController@toggle_titulo_visible')
                ->name('youtube_videos.toggle.titulo_visible');
            Route::post('/youtube_videos/{actividad}/toggle_descripcion_visible/{youtube_video}', 'YoutubeVideoController@toggle_descripcion_visible')
                ->name('youtube_videos.toggle.descripcion_visible');

            // Clonador de IntellijProject
            Route::get('/intellij_projects/copia', 'IntellijProjectController@copia')
                ->name('intellij_projects.copia');
            Route::post('/intellij_projects/duplicar', 'IntellijProjectController@duplicar')
                ->name('intellij_projects.duplicar');
            Route::delete('/intellij_projects/borrar/{id}', 'IntellijProjectController@borrar')
                ->name('intellij_projects.borrar');

            // Descargar proyectos de IntellijProject
            Route::get('/intellij_projects/descargar', 'IntellijProjectController@descargar_repos')
                ->name('intellij_projects.descargar');
            Route::post('/intellij_projects/descargar_repos', 'IntellijProjectController@descargar_repos')
                ->name('intellij_projects.descargar.repos');
            Route::post('/intellij_projects/descargar_plantillas', 'IntellijProjectController@descargar_plantillas')
                ->name('intellij_projects.descargar.plantillas');

            // IntellijProject
            Route::resource('intellij_projects', 'IntellijProjectController');
            Route::get('/intellij_projects/{actividad}/actividad', 'IntellijProjectController@actividad')
                ->name('intellij_projects.actividad');
            Route::post('/intellij_projects/{actividad}/asociar', 'IntellijProjectController@asociar')
                ->name('intellij_projects.asociar');
            Route::delete('/intellij_projects/{actividad}/desasociar/{intellij_project}', 'IntellijProjectController@desasociar')
                ->name('intellij_projects.desasociar');
            Route::post('/intellij_projects/{actividad}/toggle_titulo_visible/{intellij_project}', 'IntellijProjectController@toggle_titulo_visible')
                ->name('intellij_projects.toggle.titulo_visible');
            Route::post('/intellij_projects/{actividad}/toggle_descripcion_visible/{intellij_project}', 'IntellijProjectController@toggle_descripcion_visible')
                ->name('intellij_projects.toggle.descripcion_visible');

            // Bloquear y desbloquear repositorios
            Route::post('/intellij_projects/{intellij_project}/{actividad}/lock', 'IntellijProjectController@lock')
                ->name('intellij_projects.lock');
            Route::post('/intellij_projects/{intellij_project}/{actividad}/unlock', 'IntellijProjectController@unlock')
                ->name('intellij_projects.unlock');

            // MarkdownText
            Route::resource('markdown_texts', 'MarkdownTextController');
            Route::get('/markdown_texts/{actividad}/actividad', 'MarkdownTextController@actividad')
                ->name('markdown_texts.actividad');
            Route::post('/markdown_texts/{actividad}/asociar', 'MarkdownTextController@asociar')
                ->name('markdown_texts.asociar');
            Route::delete('/markdown_texts/{actividad}/desasociar/{markdown_text}', 'MarkdownTextController@desasociar')
                ->name('markdown_texts.desasociar');

            // Cuestionario
            Route::resource('cuestionarios', 'CuestionarioController');
            Route::get('/cuestionarios/{actividad}/actividad', 'CuestionarioController@actividad')
                ->name('cuestionarios.actividad');
            Route::post('/cuestionarios/{actividad}/asociar', 'CuestionarioController@asociar')
                ->name('cuestionarios.asociar');
            Route::delete('/cuestionarios/{actividad}/desasociar/{cuestionario}', 'CuestionarioController@desasociar')
                ->name('cuestionarios.desasociar');
            Route::resource('preguntas', 'PreguntaController');
            Route::get('/preguntas/{cuestionario}/anyadir', 'PreguntaController@anyadir')
                ->name('preguntas.anyadir');
            Route::resource('items', 'ItemController');
            Route::get('/items/{pregunta}/anyadir', 'ItemController@anyadir')
                ->name('items.anyadir');

            // FileUpload
            Route::resource('file_uploads', 'FileUploadController');
            Route::get('/file_uploads/{actividad}/actividad', 'FileUploadController@actividad')
                ->name('file_uploads.actividad');
            Route::post('/file_uploads/{actividad}/asociar', 'FileUploadController@asociar')
                ->name('file_uploads.asociar');
            Route::delete('/file_uploads/{actividad}/desasociar/{file_upload}', 'FileUploadController@desasociar')
                ->name('file_uploads.desasociar');
            Route::post('/file_uploads/{actividad}/toggle_titulo_visible/{file_upload}', 'FileUploadController@toggle_titulo_visible')
                ->name('file_uploads.toggle.titulo_visible');
            Route::post('/file_uploads/{actividad}/toggle_descripcion_visible/{file_upload}', 'FileUploadController@toggle_descripcion_visible')
                ->name('file_uploads.toggle.descripcion_visible');

            // FileResource
            Route::resource('file_resources', 'FileResourceController');
            Route::get('/file_resources/{actividad}/actividad', 'FileResourceController@actividad')
                ->name('file_resources.actividad');
            Route::post('/file_resources/{actividad}/asociar', 'FileResourceController@asociar')
                ->name('file_resources.asociar');
            Route::delete('/file_resources/{actividad}/desasociar/{file_resource}', 'FileResourceController@desasociar')
                ->name('file_resources.desasociar');
            Route::post('/files/reordenar/{a1}/{a2}', 'FileController@reordenar')
                ->name('files.reordenar');
            Route::post('/file_resources/{actividad}/toggle_titulo_visible/{file_resource}', 'FileResourceController@toggle_titulo_visible')
                ->name('file_resources.toggle.titulo_visible');
            Route::post('/file_resources/{actividad}/toggle_descripcion_visible/{file_resource}', 'FileResourceController@toggle_descripcion_visible')
                ->name('file_resources.toggle.descripcion_visible');

            // LinkCollection
            Route::resource('link_collections', 'LinkCollectionController');
            Route::get('/link_collections/{actividad}/actividad', 'LinkCollectionController@actividad')
                ->name('link_collections.actividad');
            Route::post('/link_collections/{actividad}/asociar', 'LinkCollectionController@asociar')
                ->name('link_collections.asociar');
            Route::delete('/link_collections/{actividad}/desasociar/{link_collection}', 'LinkCollectionController@desasociar')
                ->name('link_collections.desasociar');
            Route::post('/links', 'LinkController@store')->name('links.store');
            Route::delete('/links/{link}', 'LinkController@destroy')->name('links.destroy');
            Route::post('/links/reordenar/{a1}/{a2}', 'LinkController@reordenar')
                ->name('links.reordenar');
            Route::post('/link_collections/{actividad}/toggle_titulo_visible/{link_collection}', 'LinkCollectionController@toggle_titulo_visible')
                ->name('link_collections.toggle.titulo_visible');
            Route::post('/link_collections/{actividad}/toggle_descripcion_visible/{link_collection}', 'LinkCollectionController@toggle_descripcion_visible')
                ->name('link_collections.toggle.descripcion_visible');

            // Modificar la nota manualmente
            Route::get('/profesor/{user}/{curso}/nota_manual', 'ProfesorController@editNotaManual')
                ->name('profesor.nota_manual.edit');
            Route::post('/profesor/{user}/{curso}/nota_manual', 'ProfesorController@updateNotaManual')
                ->name('profesor.nota_manual.update');

            // Asignar tareas a equipos
            Route::post('/teams/filtro', 'TeamController@index')
                ->name('teams.index.filtro');
            Route::post('/profesor/asignar_tareas_equipo', 'ProfesorController@asignarTareasEquipo')
                ->name('profesor.asignar_tareas_equipo');

            // Selector
            Route::resource('selectors', 'SelectorController');
            Route::get('/selectors/{actividad}/actividad', 'SelectorController@actividad')
                ->name('selectors.actividad');
            Route::post('/selectors/{actividad}/asociar', 'SelectorController@asociar')
                ->name('selectors.asociar');
            Route::delete('/selectors/{actividad}/desasociar/{selector}', 'SelectorController@desasociar')
                ->name('selectors.desasociar');
            Route::resource('rule_groups', 'RuleGroupController');
            Route::get('/rule_groups/{selector}/anyadir', 'RuleGroupController@create')
                ->name('rule_groups.anyadir');
            Route::resource('rules', 'RuleController');
            Route::get('/rules/{rule_group}/anyadir', 'RuleController@create')
                ->name('rules.anyadir');
            Route::post('/selectors/{selector}/duplicar', 'SelectorController@duplicar')
                ->name('selectors.duplicar');
        });

        // Administrador
        Route::middleware(['role:admin'])->group(function () {

            // Lista de usuarios
            Route::get('/users', 'UserController@index')
                ->name('users.index');
            Route::match(array('GET', 'POST'), '/users/filtro', 'UserController@index')
                ->name('users.index.filtro');
            Route::post('/users/acciones_grupo', 'UserController@acciones_grupo')
                ->name('users.acciones_grupo');

            // Crear un usuario manualmente
            Route::get('/users/create', 'UserController@create')
                ->name('users.create');
            Route::post('/users', 'UserController@store')
                ->name('users.store');

            // Editar usuario
            Route::get('/users/{user}/edit', 'UserController@edit')
                ->name('users.edit');
            Route::put('/users/{user}', 'UserController@update')
                ->name('users.update');
            Route::get('/users/{user}/password', 'UserController@password')
                ->name('users.password');
            Route::put('/users/{user}/password', 'UserController@updatePassword')
                ->name('users.update.password');

            // Borrar un usuario
            Route::delete('/users/{user}', 'UserController@destroy')
                ->name('users.destroy');

            // Roles
            Route::resource('roles', 'RoleController');

            // Estructura del curso
            Route::resource('cursos', 'CursoController');
            Route::resource('unidades', 'UnidadController')
                ->parameters(['unidades' => 'unidad']);
            Route::post('/actividades/{actividad}/duplicar', 'ActividadController@duplicar')
                ->name('actividades.duplicar');
            Route::post('/actividades/duplicar_grupo', 'ActividadController@duplicar_grupo')
                ->name('actividades.duplicar_grupo');
            Route::resource('actividades', 'ActividadController')
                ->parameters(['actividades' => 'actividad']);

            // CRUD - Organizaciones
            Route::resource('organizations', 'OrganizationController');

            // CRUD - Periodos
            Route::resource('periods', 'PeriodController');

            // CRUD - Categorías
            Route::resource('categories', 'CategoryController');

            // CRUD - Grupos
            Route::resource('groups', 'GroupController');

            // Borrar entradas del registro
            Route::delete('registros/{registro}', 'RegistroController@destroy')
                ->name('registros.destroy');

            // CRUD - Cualificaciones
            Route::resource('qualifications', 'QualificationController');
            Route::post('/qualifications/filtro', 'QualificationController@index')
                ->name('qualifications.index.filtro');

            // CRUD - Competencias
            Route::resource('skills', 'SkillController');
            Route::post('/skills/filtro', 'SkillController@index')
                ->name('skills.index.filtro');

            // Filtros por curso
            Route::post('/unidades/filtro', 'UnidadController@index')
                ->name('unidades.index.filtro');
            Route::post('/actividades/filtro', 'ActividadController@index')
                ->name('actividades.index.filtro');
            Route::post('/intellij_projects/filtro', 'IntellijProjectController@index')
                ->name('intellij_projects.index.filtro');
            Route::post('/markdown_texts/filtro', 'MarkdownTextController@index')
                ->name('markdown_texts.index.filtro');
            Route::post('/youtube_videos/filtro', 'YoutubeVideoController@index')
                ->name('youtube_videos.index.filtro');
            Route::post('/file_resources/filtro', 'FileResourceController@index')
                ->name('file_resources.index.filtro');
            Route::post('/file_uploads/filtro', 'FileUploadController@index')
                ->name('file_uploads.index.filtro');
            Route::post('/cuestionarios/filtro', 'CuestionarioController@index')
                ->name('cuestionarios.index.filtro');
            Route::post('/link_collections/filtro', 'LinkCollectionController@index')
                ->name('link_collections.index.filtro');
            Route::post('/selectors/filtro', 'SelectorController@index')
                ->name('selectors.index.filtro');

            // CRUD - Feedbacks
            Route::resource('feedbacks', 'FeedbackController');
            Route::post('/feedback_mensaje', 'FeedbackController@save')
                ->name('feedbacks.save');
            Route::get('/feedbacks/{actividad}/create_actividad', 'FeedbackController@create_actividad')
                ->name('feedbacks.create_actividad');

            // CRUD - Milestones
            Route::resource('milestones', 'MilestoneController');
            Route::post('/milestones/filtro', 'MilestoneController@index')
                ->name('milestones.index.filtro');

            // Visor de logs: https://github.com/rap2hpoutre/laravel-log-viewer
            Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')
                ->name('logs');

            // Ver entradas en el registro
            Route::get('/registros', 'RegistroController@index')
                ->name('registros.index');

            // Filtrar el registro por alumno
            Route::get('/registros_alumno', 'RegistroController@index')
                ->name('registros_alumno.index');
            Route::post('/registros_alumno', 'RegistroController@index')
                ->name('registros_alumno.alumno');

            // Probar notificaciones
            Route::get('/notifications/test', 'NotificationController@test')
                ->name('notifications.test');

            // Activación manual de usuario
            Route::post('/user_manual_activation', 'UserController@manualActivation')
                ->name('users.manual_activation');

            // Bloqueo/desbloqueo del usuario
            Route::post('/user_toggle_blocked', 'UserController@toggleBlocked')
                ->name('users.toggle_blocked');

            // Informe de todas las actividades del curso
            Route::get('/actividades_export', 'ActividadController@export')
                ->withoutMiddleware('localeCookieRedirect')
                ->name('actividades.export');

            // Exportar/importar cursos
            Route::post('/cursos/{curso}/export', 'CursoController@export')
                ->name('cursos.export');
            Route::post('/cursos.import', 'CursoController@import')
                ->name('cursos.import');

            // Reiniciar los contenidos de un curso
            Route::delete('/cursos/{curso}/reset', 'CursoController@reset')
                ->name('cursos.reset');
        });

        // Alumnos y profesores
        Route::middleware(['role:alumno|profesor'])->group(function () {

            // Crear entradas en el registro
            Route::post('/registros', 'RegistroController@store')
                ->name('registros.store');

            // Responder a cuestionarios
            Route::put('/cuestionarios/{cuestionario}/respuesta', 'CuestionarioController@respuesta')
                ->name('cuestionarios.respuesta');

            // Previsualizar una actividad
            Route::get('/actividades/{actividad}/preview', 'ActividadController@preview')
                ->name('actividades.preview');
        });

        // Profesor y tutor
        Route::middleware(['role:profesor|tutor'])->group(function () {

            // Informe de grupo
            Route::get('/tutor', 'TutorController@index')
                ->name('tutor.index');
            Route::post('/tutor', 'TutorController@index')
                ->name('tutor.index.filtro');
            Route::get('/tutor/export', 'TutorController@export')
                ->withoutMiddleware('localeCookieRedirect')
                ->name('tutor.export');
            Route::get('/tutor/tareas_enviadas', 'TutorController@tareas_enviadas')
                ->name('tutor.tareas_enviadas');

            // Ver resultados de otros alumnos
            Route::post('/results', 'ResultController@index')
                ->name('results.alumno');

            // Resultados de otro usuario en PDF
            Route::post('/results/pdf', 'ResultController@pdf')
                ->name('results.pdf.filtro');

            // Ver el progreso de otros alumnos
            Route::post('/archivo/outline', 'ArchivoController@outline')
                ->name('archivo.outline.filtro');

            // Ver archivo de otros alumnos
            Route::post('/archivo', 'ArchivoController@index')
                ->name('archivo.alumno');
        });

        // Alumno, profesor y tutor
        Route::middleware(['role:alumno|profesor|tutor'])->group(function () {

            // Resultados propios
            Route::get('/results', 'ResultController@index')
                ->name('results.index');

            // Resultados propios en PDF
            Route::get('/results/pdf', 'ResultController@pdf')
                ->name('results.pdf');

            // Resultados de otras evaluaciones
            Route::get('/results/milestone', 'ResultController@index')
                ->name('results.milestone.index');
            Route::post('/results/milestone', 'ResultController@index')
                ->name('results.milestone');

            // Archivo propio
            Route::get('/archivo/{actividad}', 'ArchivoController@show')
                ->name('archivo.show');
            Route::get('/archivo', 'ArchivoController@index')
                ->name('archivo.index');

            // Esquema del curso
            Route::get('/outline', 'ArchivoController@outline')
                ->name('archivo.outline');
        });

        // Profesor y administrador
        Route::middleware(['role:profesor|admin'])->group(function () {

            // CRUD - Equipos
            Route::resource('teams', 'TeamController');
        });

        // Mensajes
        Route::group(['prefix' => 'messages'], function () {
            Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
            Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
            Route::post('create', ['as' => 'messages.create-with-subject', 'uses' => 'MessagesController@create']);
            Route::post('create_team', ['as' => 'messages.create-with-subject-team', 'uses' => 'MessagesController@create_team']);
            Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
            Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
            Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
            Route::delete('/delete_thread/{id}', ['as' => 'messages.destroy', 'uses' => 'MessagesController@destroy']);
            Route::delete('/delete_message/{id}', ['as' => 'messages.destroy_message', 'uses' => 'MessagesController@destroyMessage']);
        });

        // Pruebas
        if (config('app.debug')) {
            Route::get('/files', 'FileController@getFiles')->name('files');
        }
    });
});

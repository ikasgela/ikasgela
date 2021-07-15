<?php

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

require __DIR__ . '/profile/profile.php';

// Control de la barra lateral
Route::post('/settings/api', 'SettingController@api')
    ->name('settings.api');

// Sesión iniciada y cuenta verificada
Route::middleware(['auth', 'verified'])->group(function () {

    // Perfil de usuario
    Route::post('/users/toggle_help', 'UserController@toggle_help')
        ->name('users.toggle_help');

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
            ->name('intellij_projects.download');
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

        // YoutubeVideo
        Route::resource('youtube_videos', 'YoutubeVideoController');
        Route::get('/youtube_videos/{actividad}/actividad', 'YoutubeVideoController@actividad')
            ->name('youtube_videos.actividad');
        Route::post('/youtube_videos/{actividad}/asociar', 'YoutubeVideoController@asociar')
            ->name('youtube_videos.asociar');
        Route::delete('/youtube_videos/{actividad}/desasociar/{youtube_video}', 'YoutubeVideoController@desasociar')
            ->name('youtube_videos.desasociar');

        // Clonador de IntellijProject
        Route::get('/intellij_projects/copia', 'IntellijProjectController@copia')
            ->name('intellij_projects.copia');
        Route::post('/intellij_projects/duplicar', 'IntellijProjectController@duplicar')
            ->name('intellij_projects.duplicar');
        Route::delete('/intellij_projects/borrar/{id}', 'IntellijProjectController@borrar')
            ->name('intellij_projects.borrar');

        // Descargar proyectos de IntellijProject
        Route::get('/intellij_projects/descargar', 'IntellijProjectController@descargar')
            ->name('intellij_projects.descargar');
        Route::post('/intellij_projects/descargar', 'IntellijProjectController@descargar')
            ->name('intellij_projects.descargar.repos');

        // IntellijProject
        Route::resource('intellij_projects', 'IntellijProjectController');
        Route::get('/intellij_projects/{actividad}/actividad', 'IntellijProjectController@actividad')
            ->name('intellij_projects.actividad');
        Route::post('/intellij_projects/{actividad}/asociar', 'IntellijProjectController@asociar')
            ->name('intellij_projects.asociar');
        Route::delete('/intellij_projects/{actividad}/desasociar/{intellij_project}', 'IntellijProjectController@desasociar')
            ->name('intellij_projects.desasociar');

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

        // FileResource
        Route::resource('file_resources', 'FileResourceController');
        Route::get('/file_resources/{actividad}/actividad', 'FileResourceController@actividad')
            ->name('file_resources.actividad');
        Route::post('/file_resources/{actividad}/asociar', 'FileResourceController@asociar')
            ->name('file_resources.asociar');
        Route::delete('/file_resources/{actividad}/desasociar/{file_resource}', 'FileResourceController@desasociar')
            ->name('file_resources.desasociar');

        // Ver archivo de otros alumnos
        Route::post('/archivo', 'ArchivoController@index')
            ->name('archivo.alumno');

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
    });

    // Administrador
    Route::middleware(['role:admin'])->group(function () {

        // Lista de usuarios
        Route::get('/users', 'UserController@index')
            ->name('users.index');

        // Editar usuario
        Route::get('/users/{user}/edit', 'UserController@edit')
            ->name('users.edit');
        Route::put('/users/{user}', 'UserController@update')
            ->name('users.update');

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

        // CRUD - Feedbacks
        Route::resource('feedbacks', 'FeedbackController');
        Route::post('/feedback_mensaje', 'FeedbackController@save')
            ->name('feedback.save');

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

        // Test de stress de GitLab
        Route::get('/test_gitlab', 'IntellijProjectController@testGitLab')
            ->name('intellij_projects.test_gitlab');

        // Activación manual de usuario
        Route::post('/user_manual_activation', 'UserController@manualActivation')
            ->name('users.manual_activation');

        // Bloqueo/desbloqueo del usuario
        Route::post('/user_toggle_blocked', 'UserController@toggleBlocked')
            ->name('users.toggle_blocked');

        // Informe de todas las actividades del curso
        Route::get('/actividades_export', 'ActividadController@export')
            ->name('actividades.export');

        // Exportar/importar cursos
        Route::post('/cursos/{curso}/export', 'CursoController@export')
            ->name('cursos.export');
        Route::post('/cursos.import', 'CursoController@import')
            ->name('cursos.import');
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
            ->name('tutor.export');
        Route::get('/tutor/tareas_enviadas', 'TutorController@tareas_enviadas')
            ->name('tutor.tareas_enviadas');

        // Ver resultados de otros alumnos
        Route::post('/results', 'ResultController@index')
            ->name('results.alumno');

        // Resultados de otro usuario en PDF
        Route::post('/results/pdf', 'ResultController@pdf')
            ->name('results.pdf.filtro');
    });

    // Alumno, profesor y tutor
    Route::middleware(['role:alumno|profesor|tutor'])->group(function () {

        // Resultados propios
        Route::get('/results', 'ResultController@index')
            ->name('results.index');

        // Resultados propios en PDF
        Route::get('/results/pdf', 'ResultController@pdf')
            ->name('results.pdf');

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

        // MongoDB - Documentos
        Route::resource('documentos', 'DocumentoController');
    }
});

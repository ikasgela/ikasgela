<?php

// Página principal
Route::get('/', 'HomeController@index')
    ->name('portada');

// Páginas públicas
//Route::view('/documentacion', 'documentacion.index');

// Gestión de usuarios
Auth::routes(['verify' => true]);
require __DIR__ . '/profile/profile.php';

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
    Route::post('/uploads', 'FileController@postUpload')->name('uploadfile');
    Route::delete('/uploads/{file}', 'FileController@postDelete')->name('deletefile');
    Route::post('/files/{file}/rotate_left', 'FileController@rotateLeft')->name('files.rotate_left');
    Route::post('/files/{file}/rotate_right', 'FileController@rotateRight')->name('files.rotate_right');

    // Ajustes de notificaciones
    Route::get('/notifications', 'NotificationController@edit')
        ->name('notifications.edit');
    Route::put('/notifications', 'NotificationController@update')
        ->name('notifications.update');

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
    });

    // Profesor
    Route::middleware(['role:profesor'])->group(function () {

        // Panel de control
        Route::get('/alumnos', 'ProfesorController@index')
            ->name('profesor.index');

        // Selector de unidad
        Route::post('/alumnos', 'ProfesorController@index')
            ->name('profesor.index');

        // Asignar una tarea a un alumno
        Route::post('/alumnos/asignar_tareas', 'ProfesorController@asignarTareasGrupo')
            ->name('profesor.asignar_tareas_grupo');

        // Tareas actuales de un alumno
        Route::get('/alumnos/{user}/tareas', 'ProfesorController@tareas')
            ->name('profesor.tareas');

        // Selector de unidad
        Route::post('/alumnos/{user}/tareas', 'ProfesorController@tareas')
            ->name('profesor.tareas');

        // Asignar una tarea a un alumno
        Route::post('/alumnos/{user}/asignar_tarea', 'ProfesorController@asignarTarea')
            ->name('profesor.asignar_tarea');

        // Mostrar una tarea para revisar
        Route::get('/profesor/{user}/revisar/{tarea}', 'ProfesorController@revisar')
            ->name('profesor.revisar');

        // Borrar una tarea
        Route::delete('/tareas/{user}/destroy/{tarea}', 'TareaController@destroy')
            ->name('tareas.destroy');

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
            ->name('actividades.plantillas');

        // Previsualizar una actividad
        Route::get('/actividades/{actividad}/preview', 'ActividadController@preview')
            ->name('actividades.preview');

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

        // IntellijProject
        Route::resource('intellij_projects', 'IntellijProjectController');
        Route::get('/intellij_projects/{actividad}/actividad', 'IntellijProjectController@actividad')
            ->name('intellij_projects.actividad');
        Route::post('/intellij_projects/{actividad}/asociar', 'IntellijProjectController@asociar')
            ->name('intellij_projects.asociar');
        Route::delete('/intellij_projects/{actividad}/desasociar/{intellij_project}', 'IntellijProjectController@desasociar')
            ->name('intellij_projects.desasociar');

        // Bloquear y desbloquear repositorios
        Route::post('/intellij_projects/{repositorio}/lock', 'IntellijProjectController@lock')
            ->name('intellij_projects.lock');
        Route::post('/intellij_projects/{repositorio}/unlock', 'IntellijProjectController@unlock')
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

        // Ver archivo de otros alumnos
        Route::post('/archivo', 'ArchivoController@index')
            ->name('archivo.alumno');
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

        // CRUD - Equipos
        Route::resource('teams', 'TeamController');

        // Borrar entradas del registro
        Route::delete('registros/{registro}', 'RegistroController@destroy')
            ->name('registros.destroy');

        // CRUD - Cualificaciones
        Route::resource('qualifications', 'QualificationController');

        // CRUD - Competencias
        Route::resource('skills', 'SkillController');

        // CRUD - Feedbacks
        Route::resource('feedbacks', 'FeedbackController');

        // Visor de logs: https://github.com/rap2hpoutre/laravel-log-viewer
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

        // Ver entradas en el registro
        Route::get('/registros', 'RegistroController@index')
            ->name('registros.index');

        // Probar notificaciones
        Route::get('/notifications/test', 'NotificationController@test')
            ->name('notifications.test');
    });

    // Alumnos y profesores
    Route::middleware(['role:alumno|profesor'])->group(function () {

        // Crear entradas en el registro
        Route::post('/registros', 'RegistroController@store')
            ->name('registros.store');

        // Responder a cuestionarios
        Route::put('/cuestionarios/{cuestionario}/respuesta', 'CuestionarioController@respuesta')
            ->name('cuestionarios.respuesta');
    });

    // Profesor y tutor
    Route::middleware(['role:profesor|tutor'])->group(function () {

        // Informe de grupo
        Route::get('/tutor', 'TutorController@index')
            ->name('tutor.index');
        Route::post('/tutor', 'TutorController@index')
            ->name('tutor.index');
        Route::get('/tutor/export', 'TutorController@export')
            ->name('tutor.export');

        // Ver resultados de otros alumnos
        Route::post('/results', 'ResultController@index')
            ->name('results.alumno');
    });

    // Alumno, profesor y tutor
    Route::middleware(['role:alumno|profesor|tutor'])->group(function () {

        // Resultados propios
        Route::get('/results', 'ResultController@index')
            ->name('results.index');

        // Archivo propio
        Route::get('/archivo/{actividad}', 'ArchivoController@show')
            ->name('archivo.show');
        Route::get('/archivo', 'ArchivoController@index')
            ->name('archivo.index');
    });

    // Mensajes
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
        Route::get('all', ['as' => 'messages.all', 'uses' => 'MessagesController@all']);
        Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
        Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
        Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
        Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
        Route::delete('{id}', ['as' => 'messages.destroy', 'uses' => 'MessagesController@destroy']);
    });

    // Pruebas
    if (config('app.debug')) {
        Route::view('/tarjeta_si_no', 'tarjetas.si_no');
        Route::view('/tarjeta_video', 'tarjetas.video');
        Route::view('/tarjeta_respuesta_multiple', 'tarjetas.respuesta_multiple');
        Route::view('/tarjeta_respuesta_corta', 'tarjetas.respuesta_corta');
        Route::get('/tarjeta_texto_markdown', 'TarjetaController@texto_markdown');
        Route::view('/tarjeta_pdf', 'tarjetas.pdf');
        Route::view('/reloj', 'tarjetas.reloj');

        Route::get('/files', 'FileController@getFiles')->name('files');
    }
});

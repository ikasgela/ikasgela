<?php

// Página principal
Route::get('/', 'HomeController@index')
    ->name('portada');

// Páginas públicas
Route::view('/documentacion', 'documentacion.index');

// Gestión de usuarios
Auth::routes(['verify' => true]);
require __DIR__ . '/profile/profile.php';

// Sesión iniciada y cuenta verificada
Route::middleware(['auth', 'verified'])->group(function () {

    // Perfil de usuario
    Route::get('/home', 'HomeController@home')
        ->name('users.home');
    Route::post('/users/toggle_help', 'UserController@toggle_help')
        ->name('users.toggle_help');

    // Actualizar estado de una tarea
    Route::put('/actividades/{tarea}/estado', 'ActividadController@actualizarEstado')
        ->name('actividades.estado');

    // Alumno
    Route::middleware(['role:alumno'])->group(function () {

        // Mostrar el escritorio del alumno
        Route::get('/home', 'AlumnoController@tareas')
            ->name('users.home');

        // Archivo
        Route::get('/archivo/{actividad}', 'ArchivoController@show')
            ->name('archivo.show');
        Route::get('/archivo', 'ArchivoController@index')
            ->name('archivo.index');

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

        // IntellijProject
        Route::resource('intellij_projects', 'IntellijProjectController');
        Route::get('/intellij_projects/{actividad}/actividad', 'IntellijProjectController@actividad')
            ->name('intellij_projects.actividad');
        Route::post('/intellij_projects/{actividad}/asociar', 'IntellijProjectController@asociar')
            ->name('intellij_projects.asociar');
        Route::delete('/intellij_projects/{actividad}/desasociar/{intellij_project}', 'IntellijProjectController@desasociar')
            ->name('intellij_projects.desasociar');

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
    });

    // Alumnos y profesores
    Route::middleware(['role:alumno|profesor'])->group(function () {

        // Ver y crear entradas en el registro
        Route::get('/registros', 'RegistroController@index')
            ->name('registros.index');
        Route::post('/registros', 'RegistroController@store')
            ->name('registros.store');
    });

    // Pruebas
    Route::view('/tarjeta_si_no', 'tarjetas.si_no');
    Route::view('/tarjeta_video', 'tarjetas.video');
    Route::view('/tarjeta_respuesta_multiple', 'tarjetas.respuesta_multiple');
    Route::view('/tarjeta_respuesta_corta', 'tarjetas.respuesta_corta');
    Route::get('/tarjeta_texto_markdown', 'TarjetaController@texto_markdown');
    Route::view('/tarjeta_pdf', 'tarjetas.pdf');
    Route::view('/results', 'results.index')->name('results.index');;

    // Mensaje
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
        Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
        Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
        Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
        Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
    });
});

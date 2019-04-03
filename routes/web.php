<?php

// Página principal
Route::get('/', 'HomeController@index')
    ->name('portada');

// Páginas públicas
Route::view('/documentacion', 'documentacion.index');

// Gestión de usuarios
Auth::routes(['verify' => true]);
require __DIR__ . '/profile/profile.php';

// Perfil de usuario
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', 'HomeController@home')
        ->name('users.home');
    Route::view('/dashboard', 'users.dashboard')
        ->name('users.dashboard');
    Route::post('/users/toggle_help', 'UserController@toggle_help')
        ->name('users.toggle_help');
});

// Alumno
Route::middleware(['auth', 'role:alumno'])->group(function () {

    Route::put('/actividades/{tarea}/estado', 'ActividadController@actualizarEstado')
        ->name('actividades.estado');
    Route::get('/actividades/archivo', 'ActividadController@archivo')
        ->name('actividades.archivo');

    // IntellijProject
    Route::get('/intellij_projects/{actividad}/fork/{intellij_project}', 'IntellijProjectController@fork')
        ->name('intellij_projects.fork');
});

// Administrador
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/actividades/plantillas', 'ActividadController@plantillas')
        ->name('actividades.plantillas');

    // Estructura del curso
    Route::resource('cursos', 'CursoController');
    Route::resource('unidades', 'UnidadController')
        ->parameters(['unidades' => 'unidad']);
    Route::resource('actividades', 'ActividadController')
        ->parameters(['actividades' => 'actividad']);

    // YoutubeVideo
    Route::resource('youtube_videos', 'YoutubeVideoController');
    Route::get('/youtube_videos/{actividad}/actividad', 'YoutubeVideoController@actividad')
        ->name('youtube_videos.actividad');
    Route::post('/youtube_videos/{actividad}/asociar', 'YoutubeVideoController@asociar')
        ->name('youtube_videos.asociar');
    Route::delete('/youtube_videos/{actividad}/desasociar/{youtube_video}', 'YoutubeVideoController@desasociar')
        ->name('youtube_videos.desasociar');

    // IntellijProject
    Route::get('/intellij_projects/copia', 'IntellijProjectController@copia')
        ->name('intellij_projects.copia');
    Route::post('/intellij_projects/duplicar', 'IntellijProjectController@duplicar')
        ->name('intellij_projects.duplicar');

    Route::resource('intellij_projects', 'IntellijProjectController');
    Route::get('/intellij_projects/{actividad}/actividad', 'IntellijProjectController@actividad')
        ->name('intellij_projects.actividad');
    Route::post('/intellij_projects/{actividad}/asociar', 'IntellijProjectController@asociar')
        ->name('intellij_projects.asociar');
    Route::delete('/intellij_projects/{actividad}/desasociar/{intellij_project}', 'IntellijProjectController@desasociar')
        ->name('intellij_projects.desasociar');

});

// Profesor
Route::middleware(['auth', 'role:profesor'])->group(function () {
    Route::get('/users', 'UserController@index')
        ->name('users.index');
    Route::get('/tareas/{user}', 'TareaController@index')
        ->name('tareas.index');
    Route::post('/tareas/{user}/asignar', 'TareaController@asignar')
        ->name('tareas.asignar');
    Route::delete('/tareas/{user}/destroy/{tarea}', 'TareaController@destroy')
        ->name('tareas.destroy');
});

// Pruebas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/tarjeta_si_no', 'tarjetas.si_no');
    Route::view('/tarjeta_video', 'tarjetas.video');
    Route::view('/tarjeta_respuesta_multiple', 'tarjetas.respuesta_multiple');
    Route::view('/tarjeta_respuesta_corta', 'tarjetas.respuesta_corta');
    Route::get('/tarjeta_texto_markdown', 'TarjetaController@texto_markdown');
    Route::view('/tarjeta_pdf', 'tarjetas.pdf');
});

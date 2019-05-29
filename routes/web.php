<?php
Route::domain('{organization}.' . env('SITE_URL', 'ikasgela.com'))->middleware(['organization'])->group(function () {
    require __DIR__ . '/ikasgela.php';
});

Route::middleware(['organization'])->group(function () {
    require __DIR__ . '/ikasgela.php';
});

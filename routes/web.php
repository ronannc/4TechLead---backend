<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return response()->file(resource_path('docs/index.html'));
})->name('docs');

Route::get('/docs/openapi', function () {
    return response()->json(
        json_decode((string) file_get_contents(resource_path('docs/openapi.json')), true, flags: JSON_THROW_ON_ERROR)
    );
})->name('docs.openapi');

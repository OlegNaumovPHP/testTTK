<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'create']);

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

Route::get('/getUserRole', [App\Http\Controllers\Auth\RegisterController::class, 'getUserRole']);

Route::get('/regRoles', [App\Http\Controllers\Auth\RegisterController::class, 'regRoles']);

Route::any('{all?}', function () {

    return view('app');
})->where(['all' => '.*']);

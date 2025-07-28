<?php

use Illuminate\Support\Facades\Route;

//Rutas para login
Route::get('/', function () {
    return view('vista_login.login');
})->name('login');

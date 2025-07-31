<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use Illuminate\Support\Facades\Route;

//Rutas para mostrar el login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

//Rutas para procesar el login
Route::post('/login', [LoginController::class, 'login'])->name('login');

//Ruta definitiva para redirigir al login
Route::get('/', function () {
    return redirect()->route('login');
});



//Rutas para los registros
Route::get('/registros', [RegistroController::class, 'mostrarRegistros'])->name('registros')->middleware('auth');
Route::post('/agregar', [RegistroController::class, 'registrar'])->name('agregar')->middleware('auth');
Route::post('/editar/{id}', [RegistroController::class, 'editarRegistros'])->name('editar')->middleware('auth');

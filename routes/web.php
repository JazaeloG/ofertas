<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfertaController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Ruta para mostrar el formulario de edición de una oferta
Route::get('/oferta/{nombre}', [OfertaController::class, 'ver'])->name('oferta.ver');
Route::get('/ofertas/{id}/edit', [App\Http\Controllers\OfertaController::class, 'edit'])->name('ofertas.edit');
Route::get('/ofertas', [App\Http\Controllers\OfertaController::class, 'index'])->name('ofertas.index');
Route::post('/ofertas', [App\Http\Controllers\OfertaController::class, 'store'])->name('ofertas.store');
Route::patch('/ofertas/{id}', [App\Http\Controllers\OfertaController::class, 'update'])->name('ofertas.update');
Route::delete('/ofertas/{id}', [App\Http\Controllers\OfertaController::class, 'destroy'])->name('ofertas.destroy');
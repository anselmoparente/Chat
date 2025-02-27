<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MensagemController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::redirect("/", "/home");
Route::resource('/home', HomeController::class)->only(['index']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::put('/usuario/{id}', [UsuarioController::class, 'update']);

Route::get('/mensagens/{id}', [MensagemController::class, 'index']);
Route::post('/mensagens', [MensagemController::class, 'store']);
<?php

use Illuminate\Support\Facades\Route;
use Src\Usuario\Auth\Infrastructure\Web\LoginController;

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Fichero de rutas configurado en el RouteServiceProvider
| Pasa los siguintes middlewares: ['api', 'auth:sanctum', 'throttle: 60,1']
| Prefijo: api/usuarios
|
*/

Route::post('login', [LoginController::class, 'login'])->withoutMiddleware('auth:sanctum');
Route::get('/user', [LoginController::class, 'self']);
Route::post('/logout', [LoginController::class, 'logout']);

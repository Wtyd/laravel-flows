<?php

use Illuminate\Support\Facades\Route;
use Src\Usuario\Gestion\Infrastructure\Web\UsuarioController;

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

Route::group(['middleware' => ['role_or_permission:editar_usuario|ver_usuario|administrador']], function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::get('/{id}', [UsuarioController::class, 'show'])->where('id', '[0-9]+');
});

Route::group(['middleware' => ['permission:editar_usuario']], function () {
    Route::post('', [UsuarioController::class, 'store']);
    // Route::put('/{id}', [UsuarioController::class, 'actualizarPorId']);
    Route::put('/{user}', [UsuarioController::class, 'update']);
});

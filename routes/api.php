<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideosController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscriptionsController;
use App\Http\Controllers\Api\CommentController;

Route::get('/users', [UserController::class, 'index']);
//Ruta para registrar usuarios en el sistema
Route::post('/register', [AuthController::class, 'register']);
//Ruta para que los usuarios inicien sesión en el sistema
Route::post('/login', [AuthController::class, 'login']);

//Ruta que regresa una lista de usuarios
Route::get('/users', [UserController::class, 'users']);
//Ruta que regresa la información de un usuario en especifico y sus videos
Route::get('/users/{userId?}', [UserController::class, 'users']);

//Ruta para obtener la información de un video en especifico
Route::get('/video/{videoId?}', [VideosController::class, 'getVideo']);
//Ruta para buscar videos
Route::post('/search', [VideosController::class, 'search']);

//Las rutas definidas dentro de este grupo serán solo accesibles si el usuario ha iniciado sesión
Route::group(['middleware' => ['auth:sanctum']], function(){
    //Obtener perfil del usuario actual
    Route::get('/user', [UserController::class, 'userProfile']);
    //Eliminar la cuenta (videos, comentarios, etc) del usuario
    Route::delete('/user', [UserController::class, 'deleteUser']);

    // Route::get('/users', [UserController::class, 'users']);
    // Route::get('/users/{userId?}', [UserController::class, 'users']);
    
    //Subir foto (perfil o cover) del usuario
    Route::post('/userPhoto', [UserController::class, 'userPhoto']);

    //Ruta para crear video
    Route::post('/video', [VideosController::class, 'createVideo']);

    Route::delete('/video', [VideosController::class, 'deleteVideo']);

    //Ruta para que el usuario actual se suscriba a otro
    Route::post('/subscribeTo', [SubscriptionsController::class, 'subscribeTo']);
    //Ruta para eliminar una suscripción
    Route::post('/removeSubscription', [SubscriptionsController::class, 'removeSubscription']);
    
    //Ruta para agrear un comentario a un video
    Route::post('/comment', [CommentController::class, 'createComment']);


});
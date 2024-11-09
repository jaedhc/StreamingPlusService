<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideosController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscriptionsController;

Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/users', [UserController::class, 'users']);
Route::get('/users/{userId?}', [UserController::class, 'users']);

//Ruta para crear video
Route::post('/video', [VideosController::class, 'createVideo']);
Route::get('/video/{videoId?}', [VideosController::class, 'getVideo']);


Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/user', [UserController::class, 'userProfile']);

    Route::get('/users', [UserController::class, 'users']);
    Route::get('/users/{userId?}', [UserController::class, 'users']);
    
    Route::post('/userPhoto', [UserController::class, 'userPhoto']);

    Route::post('/subscribeTo', [SubscriptionsController::class, 'subscribeTo']);
    Route::post('/removeSubscription', [SubscriptionsController::class, 'removeSubscription']);
    
});
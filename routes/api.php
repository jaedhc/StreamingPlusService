<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideosController;
use App\Http\Controllers\Api\AuthController;

Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/user', [UserController::class, 'userProfile']);
    Route::post('/createVideo', [VideosController::class, 'createVideo']);
    Route::get('/getUserVideo', [VideosController::class, 'getUserVideos']);
});
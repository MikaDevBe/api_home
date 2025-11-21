<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Auth\UserController;

Route::prefix('v1')->group(function(){
  Route::post('login',[AuthController::class, 'login']);
  Route::post('register',[AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function(){
  Route::post('logout',[AuthController::class, 'logout']);
  Route::get('user',[UserController::class, 'show']);
  Route::post('user',[UserController::class, 'update']);
});
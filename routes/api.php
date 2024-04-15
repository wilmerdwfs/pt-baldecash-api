<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('users/create',[UsersController::class, 'create']);
    Route::get('users',[UsersController::class, 'list']);
    Route::delete('users/delete',[UsersController::class, 'delete']);
    Route::get('users/edit/{id}',[UsersController::class, 'edit']);
    Route::put('users/edit-save', [UsersController::class, 'editSave']);
    Route::post('auth/close',[AuthController::class, 'close']);
});

Route::post('auth/login',[AuthController::class, 'login']);









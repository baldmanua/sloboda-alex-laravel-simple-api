<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['guest'])->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('messages', MessageController::class);
    Route::get('tags', [TagController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

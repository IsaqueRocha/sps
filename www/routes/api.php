<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;

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

Route::post('/login', [AuthController::class, 'authenticate'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('transaction', TransactionController::class, ['only' => 'store']);
    Route::resource('profile', ProfileController::class, ['only' => 'index']);
});

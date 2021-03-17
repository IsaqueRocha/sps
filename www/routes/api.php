<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;

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

Route::post('/login', [AuthController::class, 'authenticate'])->name('user.login');
Route::post('/register', [AuthController::class, 'register'])->name('user.register');

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('transaction', TransactionController::class, ['only' => 'store']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

<?php

use App\Http\Controllers\API\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// User API routes
Route::post('/register', [ApiController::class, 'register']);
Route::get('/users', [ApiController::class, 'getUsers']);
Route::get('/users/{id}', [ApiController::class, 'getUser']);
Route::put('/users/{id}', [ApiController::class, 'updateUser']);
Route::delete('/users/{id}', [ApiController::class, 'deleteUser']);
Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [ApiController::class, 'profile']);
});

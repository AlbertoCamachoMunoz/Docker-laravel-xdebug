<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PruebasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/welcome', function () {
    echo "welcome";
    return view('welcome');
});

// Route::get('/', 'PruebasController@testOrm');
Route::get('/', [PruebasController::class, 'testOrm']);
// Route::get('/user', [UserController::class, 'pruebas']);
// Route::get('/category', [CategoryController::class, 'pruebas']);
// Route::get('/post', [PostController::class, 'pruebas']);

// rutas para el entorno
Route::post('/api/register',                [UserController::class, 'register']);
Route::post('/api/login',                   [UserController::class, 'login']);
Route::put('/api/update',                   [UserController::class, 'update'])->middleware(ApiAuthMiddleware::class);
Route::post('/api/upload',                  [UserController::class, 'upload'])->middleware(ApiAuthMiddleware::class);
Route::get('/api/detail/{id}',             [UserController::class, 'detail']);
Route::get('/api/user/avatar/{filename}',   [UserController::class, 'getImage']);

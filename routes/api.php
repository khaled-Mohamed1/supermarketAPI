<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/getusers', [AuthController::class, 'getUsers']);
});

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [UserController::class, 'categoriesIndex']);
    Route::get('/category/{id}', [UserController::class, 'categoryShow']);
    Route::get('/products', [UserController::class, 'productsIndex']);
    Route::get('/product/{id}', [UserController::class, 'productShow']);
    Route::post('/logout', [UserController::class, 'userLogout']);

});


//admin
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'create']);

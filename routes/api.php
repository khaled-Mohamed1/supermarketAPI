<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
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

Route::prefix('admin')->group(function () {
    //category
    Route::apiResource('categories', CategoryController::class);

    //product
    Route::apiResource('products', ProductController::class);

    //logout
    Route::post('/logout', [AuthController::class, 'logout']);

    //user
    Route::get('/getusers', [AuthController::class, 'getUsers']);
    Route::put('/updateuser/{id}', [AuthController::class, 'updateUser']);
    Route::delete('/deleteuser/{id}', [AuthController::class, 'deleteUser']);

    //order
    Route::apiResource('orders', OrderController::class);
    Route::get('/putdebt/{id}', [OrderController::class, 'putDebt']);

    //statistics
    Route::get('/statistics', [AuthController::class, 'statistics']);

    //debts
    Route::get('/usersdebts', [AuthController::class, 'usersDebts']);
    Route::put('/update_userdebt/{id}', [AuthController::class, 'updateUserDebt']);
});

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::post('/order', [UserController::class, 'storeOrder']);
    Route::get('/getorder', [UserController::class, 'getOrder']);
    Route::post('/logout', [UserController::class, 'userLogout']);
    Route::put('/userupdate', [UserController::class, 'userUpdate']);
});

Route::get('/categories', [UserController::class, 'categoriesIndex']); //
Route::get('/category/{id}', [UserController::class, 'categoryShow']); //
Route::get('/products', [UserController::class, 'productsIndex']); //
Route::get('/product/{id}', [UserController::class, 'productShow']); //

//admin
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'create']);
//1|bTVkBDBfGUw6VFpn81YROYlV0Iyjs23HlN4bCUM3

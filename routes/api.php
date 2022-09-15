<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OfferController;
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

//admin
Route::prefix('admin')->group(function () {
    //category
    Route::apiResource('categories', CategoryController::class);
    Route::post('/categoryupdate', [CategoryController::class, 'categoryUpdate']);
    Route::get('/categoriesnames', [CategoryController::class, 'categoriesNames']);
    Route::post('/categorydelete', [CategoryController::class, 'categoryDelete']);

    //product
    Route::apiResource('products', ProductController::class);
    Route::post('/productupdate', [ProductController::class, 'productUpdate']);
    Route::post('/productdelete', [ProductController::class, 'productDelete']);

    Route::apiResource('offers', OfferController::class);
    Route::post('/offerupdate', [OfferController::class, 'offerUpdate']);
    Route::post('/offerdelete', [OfferController::class, 'offerDelete']);


    //logout
    Route::post('/logout', [AuthController::class, 'logout']);

    //user
    Route::get('/getusers', [AuthController::class, 'getUsers']);
    Route::post('/updateuser', [AuthController::class, 'updateUser']);
    Route::post('/deleteuser', [AuthController::class, 'deleteUser']);

    //order
    Route::apiResource('orders', OrderController::class);
    Route::post('/ordershow', [OrderController::class, 'orderShow']);
    Route::post('/orderupdate', [OrderController::class, 'orderUpdate']);
    Route::post('/orderdelete', [OrderController::class, 'orderDelete']);
    Route::post('/putdebt', [OrderController::class, 'putDebt']);

    //statistics
    Route::get('/statistics', [AuthController::class, 'statistics']);

    //debts
    Route::get('/usersdebts', [AuthController::class, 'usersDebts']);
    Route::post('/update_userdebt', [AuthController::class, 'updateUserDebt']);
});

//user
Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'userLogout']);
});
Route::post('user/order', [UserController::class, 'storeOrder']);
Route::post('user/userupdate', [UserController::class, 'userUpdate']);
Route::post('user/getorder', [UserController::class, 'getOrder']);

//user cart
Route::post('user/cart', [CartController::class, 'cartList']);
Route::post('user/store_cart', [CartController::class, 'addToCart']);
Route::post('user/update_cart', [CartController::class, 'updateCart']);
Route::post('user/remove', [CartController::class, 'removeCart']);
Route::post('user/clear', [CartController::class, 'clearAllCart']);

//public
Route::get('/categories', [UserController::class, 'categoriesIndex']); //
Route::post('/category', [UserController::class, 'categoryShow']); //
Route::get('/products', [UserController::class, 'productsIndex']); //
Route::post('/product', [UserController::class, 'productShow']); //
Route::get('/offers', [UserController::class, 'offerIndex']); //
Route::get('/mostorders', [UserController::class, 'mostOrders']); //

//admin
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'create']);
//1|bTVkBDBfGUw6VFpn81YROYlV0Iyjs23HlN4bCUM3

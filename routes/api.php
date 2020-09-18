<?php

use App\Http\Controllers\Api\BarrelsController;
use App\Http\Controllers\Api\BottlesController;
use App\Http\Controllers\Api\BundlesController;
use App\Http\Controllers\Api\ContactsController;
use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\OthersController;
use App\Http\Controllers\Api\PaymentMethodsController;
use App\Http\Controllers\Api\SuppliersController;
use App\Http\Controllers\Api\TransactionsController;
use App\Http\Controllers\Api\UsersController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'prefix' => '/users'], function () {
    Route::get('/', [UsersController::class, 'index']);
    Route::post('/', [UsersController::class, 'store']);
    Route::get('/{user}', [UsersController::class, 'show']);
    Route::put('/{user}', [UsersController::class, 'update']);
    Route::delete('/{user}', [UsersController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/customers'], function () {
    Route::get('/', [CustomersController::class, 'index']);
    Route::post('/', [CustomersController::class, 'store']);
    Route::get('/{customer}', [CustomersController::class, 'show']);
    Route::put('/{customer}', [CustomersController::class, 'update']);
    Route::delete('/{customer}', [CustomersController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/suppliers'], function () {
    Route::get('/', [SuppliersController::class, 'index']);
    Route::post('/', [SuppliersController::class, 'store']);
    Route::get('/{supplier}', [SuppliersController::class, 'show']);
    Route::put('/{supplier}', [SuppliersController::class, 'update']);
    Route::delete('/{supplier}', [SuppliersController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/contacts'], function () {
    Route::get('/', [ContactsController::class, 'index']);
    Route::post('/', [ContactsController::class, 'store']);
    Route::get('/{contact}', [ContactsController::class, 'show']);
    Route::put('/{contact}', [ContactsController::class, 'update']);
    Route::delete('/{contact}', [ContactsController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/barrels'], function () {
    Route::get('/', [BarrelsController::class, 'index']);
    Route::post('/', [BarrelsController::class, 'store']);
    Route::get('/{barrel}', [BarrelsController::class, 'show']);
    Route::put('/{barrel}', [BarrelsController::class, 'update']);
    Route::delete('/{barrel}', [BarrelsController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/bottles'], function () {
    Route::get('/', [BottlesController::class, 'index']);
    Route::post('/', [BottlesController::class, 'store']);
    Route::get('/{bottle}', [BottlesController::class, 'show']);
    Route::put('/{bottle}', [BottlesController::class, 'update']);
    Route::delete('/{bottle}', [BottlesController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/food'], function () {
    Route::get('/', [FoodController::class, 'index']);
    Route::post('/', [FoodController::class, 'store']);
    Route::get('/{food}', [FoodController::class, 'show']);
    Route::put('/{food}', [FoodController::class, 'update']);
    Route::delete('/{food}', [FoodController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/others'], function () {
    Route::get('/', [OthersController::class, 'index']);
    Route::post('/', [OthersController::class, 'store']);
    Route::get('/{other}', [OthersController::class, 'show']);
    Route::put('/{other}', [OthersController::class, 'update']);
    Route::delete('/{other}', [OthersController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/bundles'], function () {
    Route::get('/', [BundlesController::class, 'index']);
    Route::post('/', [BundlesController::class, 'store']);
    Route::get('/{bundle}', [BundlesController::class, 'show']);
    Route::put('/{bundle}', [BundlesController::class, 'update']);
    Route::delete('/{bundle}', [BundlesController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/payment-methods'], function () {
    Route::get('/', [PaymentMethodsController::class, 'index']);
    Route::post('/', [PaymentMethodsController::class, 'store']);
    Route::get('/{paymentMethod}', [PaymentMethodsController::class, 'show']);
    Route::put('/{paymentMethod}', [PaymentMethodsController::class, 'update']);
    Route::delete('/{paymentMethod}', [PaymentMethodsController::class, 'destroy']);
});
Route::group(['namespace' => 'Api', 'prefix' => '/transactions'], function () {
    Route::get('/', [TransactionsController::class, 'index']);
    Route::post('/', [TransactionsController::class, 'store']);
    Route::get('/{transaction}', [TransactionsController::class, 'show']);
    Route::put('/{transaction}', [TransactionsController::class, 'update']);
    Route::delete('/{transaction}', [TransactionsController::class, 'destroy']);
});

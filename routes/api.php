<?php

use Illuminate\Http\Request;

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
    Route::get('/', 'UsersController@index');
    Route::post('/', 'UsersController@store');
    Route::get('/{user}', 'UsersController@show');
    Route::put('/{user}', 'UsersController@update');
    Route::delete('/{user}', 'UsersController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/customers'], function () {
    Route::get('/', 'CustomersController@index');
    Route::post('/', 'CustomersController@store');
    Route::get('/{customer}', 'CustomersController@show');
    Route::put('/{customer}', 'CustomersController@update');
    Route::delete('/{customer}', 'CustomersController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/suppliers'], function () {
    Route::get('/', 'SuppliersController@index');
    Route::post('/', 'SuppliersController@store');
    Route::get('/{supplier}', 'SuppliersController@show');
    Route::put('/{supplier}', 'SuppliersController@update');
    Route::delete('/{supplier}', 'SuppliersController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/contacts'], function () {
    Route::get('/', 'ContactsController@index');
    Route::post('/', 'ContactsController@store');
    Route::get('/{contact}', 'ContactsController@show');
    Route::put('/{contact}', 'ContactsController@update');
    Route::delete('/{contact}', 'ContactsController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/barrels'], function () {
    Route::get('/', 'BarrelsController@index');
    Route::post('/', 'BarrelsController@store');
    Route::get('/{barrel}', 'BarrelsController@show');
    Route::put('/{barrel}', 'BarrelsController@update');
    Route::delete('/{barrel}', 'BarrelsController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/bottles'], function () {
    Route::get('/', 'BottlesController@index');
    Route::post('/', 'BottlesController@store');
    Route::get('/{bottle}', 'BottlesController@show');
    Route::put('/{bottle}', 'BottlesController@update');
    Route::delete('/{bottle}', 'BottlesController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/food'], function () {
    Route::get('/', 'FoodController@index');
    Route::post('/', 'FoodController@store');
    Route::get('/{food}', 'FoodController@show');
    Route::put('/{food}', 'FoodController@update');
    Route::delete('/{food}', 'FoodController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/others'], function () {
    Route::get('/', 'OthersController@index');
    Route::post('/', 'OthersController@store');
    Route::get('/{other}', 'OthersController@show');
    Route::put('/{other}', 'OthersController@update');
    Route::delete('/{other}', 'OthersController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/bundles'], function () {
    Route::get('/', 'BundlesController@index');
    Route::post('/', 'BundlesController@store');
    Route::get('/{bundle}', 'BundlesController@show');
    Route::put('/{bundle}', 'BundlesController@update');
    Route::delete('/{bundle}', 'BundlesController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/payment-methods'], function () {
    Route::get('/', 'PaymentMethodsController@index');
    Route::post('/', 'PaymentMethodsController@store');
    Route::get('/{paymentMethod}', 'PaymentMethodsController@show');
    Route::put('/{paymentMethod}', 'PaymentMethodsController@update');
    Route::delete('/{paymentMethod}', 'PaymentMethodsController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/transactions'], function () {
    Route::get('/', 'TransactionsController@index');
    Route::post('/', 'TransactionsController@store');
    Route::get('/{transaction}', 'TransactionsController@show');
    Route::put('/{transaction}', 'TransactionsController@update');
    Route::delete('/{transaction}', 'TransactionsController@destroy');
});

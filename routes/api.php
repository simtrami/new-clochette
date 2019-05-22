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
Route::group(['namespace' => 'Api', 'prefix' => '/foods'], function () {
    Route::get('/', 'FoodsController@index');
    Route::post('/', 'FoodsController@store');
    Route::get('/{food}', 'FoodsController@show');
    Route::put('/{food}', 'FoodsController@update');
    Route::delete('/{food}', 'FoodsController@destroy');
});
Route::group(['namespace' => 'Api', 'prefix' => '/others'], function () {
    Route::get('/', 'OthersController@index');
    Route::post('/', 'OthersController@store');
    Route::get('/{other}', 'OthersController@show');
    Route::put('/{other}', 'OthersController@update');
    Route::delete('/{other}', 'OthersController@destroy');
});

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

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

Route::group(['middleware' => 'auth:api'], function(){

    Route::group(['prefix' => 'rooms', 'as' => 'rooms.'], function () {
        Route::post('new', 'Api\RoomController@newRoom')->name('new');
        Route::patch('update-name', 'Api\RoomController@updateRoomName')->name('update-name');
        Route::patch('update-ownership', 'Api\RoomController@updateRoomOwnership')->name('update-ownership');
        Route::delete('delete', 'Api\RoomController@deleteRoom')->name('delete');
        Route::get('list', 'Api\RoomController@index')->name('list');
    });

    Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
        Route::post('new', 'Api\MessageController@createNewMessage')->name('new');
        Route::patch('edit', 'Api\MessageController@editMessage')->name('edit');
        Route::delete('delete', 'Api\MessageController@deleteMessage')->name('delete');
        Route::get('list', 'Api\MessageController@index')->name('list');
    });

});

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

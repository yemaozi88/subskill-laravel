<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'SiteController@home');

// Eword
Route::group(['prefix' => 'eword'], function () {
    Route::get('/', 'EwordController@index');
    Route::post('intro', 'EwordController@intro');
    Route::post('quiz', 'EwordController@quiz');
    Route::post('result', 'EwordController@result');
    Route::post('summary', 'EwordController@summary');
});


// Admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth.basic'], function () {
    Route::get('/', 'AdminController@index');
    Route::get('eword', 'AdminController@eword');
    Route::get('eword_list', 'AdminController@eword_list');
});

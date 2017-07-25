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

// Listening spanning test
Route::group(['prefix' => 'lst'], function () {
    Route::get('/', 'LstController@index');
    Route::get('quiz', 'LstController@quiz');
});

// Reading spanning test
Route::group(['prefix' => 'rst'], function () {
    Route::get('/', 'RstController@index');
    Route::get('quiz', 'RstController@quiz');
});

// Digital spanning test
Route::group(['prefix' => 'dst'], function () {
    Route::get('/', 'DstController@index');
    Route::get('quiz', 'DstController@quiz');
});

// Admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth.basic'], function () {
    Route::get('/', 'AdminController@index');
    Route::group(['prefix' => 'eword', 'namespace' => 'Admin'], function () {
        Route::get('/', 'EwordController@index');
        Route::get('list_by_date', 'EwordController@list_by_date');
        Route::get('detail', 'EwordController@detail');
    });

    Route::group(['prefix' => 'memory', 'namespace' => 'Admin'], function () {
        Route::group(['prefix' => 'lst'], function () {
            Route::get('/', 'LstController@index');
            Route::get('list_by_date', 'LstController@list_by_date');
            Route::get('detail', 'LstController@detail');
        });
        Route::group(['prefix' => 'rst'], function () {
            Route::get('/', 'RstController@index');
            Route::get('list_by_date', 'RstController@list_by_date');
            Route::get('detail', 'RstController@detail');
        });
        Route::group(['prefix' => 'dst'], function () {
            Route::get('/', 'DstController@index');
            Route::get('list_by_date', 'DstController@list_by_date');
            Route::get('detail', 'DstController@detail');
        });
    });
});
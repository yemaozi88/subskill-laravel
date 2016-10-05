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
Route::get('/eword', 'EwordController@index');
Route::post('/eword/intro', 'EwordController@intro');
Route::post('/eword/quiz', 'EwordController@quiz');
Route::post('/eword/result', 'EwordController@result');
Route::post('/eword/summary', 'EwordController@summary');

// Admin

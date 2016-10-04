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
Route::get('/eword', 'EwordController@index');
Route::get('/eword/intro', 'EwordController@intro');
Route::get('/eword/quiz', 'EwordController@quiz');
Route::get('/eword/result', 'EwordController@result');
Route::get('/eword/summary', 'EwordController@summary');
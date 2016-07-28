<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'statistic'], function () {

    Route::get('/about', function () {
        return view('about');
    });

    Route::get('/contacts', function () {
        return view('contacts');
    });

    Route::get('/product/{id}', 'ProductController@show');

    Route::get('/', function () {
        return view('home');
    });
});

Route::group(['middleware' => 'auth.supersimple'], function () {
    Route::get('/admin', 'StatisticController@index');
});

<?php


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', function () {
    return 'im in api';
});

<?php

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/check', function () {
   return phpinfo();
});


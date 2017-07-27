<?php

Route::get('/notes/getall',['middleware' => 'cors','uses'=>'notes@getAll']);
Route::get('/notes/getonenote/{id}',['middleware' => 'cors','uses'=>'notes@getDetailedNote']);
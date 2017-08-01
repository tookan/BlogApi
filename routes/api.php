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

Route::group(['namespace' => 'api'], function(){

    Route::get('/notes/getallbyall/{offset}',['uses'=>'notes@getAllByAll']);
    Route::get('/notes/getonenote/{id}',['uses'=>'notes@getDetailedNote']);
    Route::get('/notes/getpagescount',['uses'=>'notes@getPagesCount']);
    Route::get('/notes/searchservice/{searchTerm}',['uses'=>'notes@searchService']);
    Route::get('/notes/getcurrentuser',['uses'=>'notes@getCurrentUser']);
    Route::get('/checkuser','notes@getUser');

    Route::post('/users/posttest',['uses'=>'users@postTest','as' => 'testing']);
    Route::post('/users/login','users@login');
});

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['namespace' => 'api'], function(){

    Route::get('/notes/getallbyall/{offset}',['uses'=>'notes@getAllByAll']);
    Route::get('/notes/user/{name}/{pageNumber}','notes@getAllByUser');
    Route::get('/notes/getonenote/{id}',['uses'=>'notes@getDetailedNote']);
    Route::get('/notes/getpagescount',['uses'=>'notes@getPagesCount']);
    Route::get('/notes/searchservice/{searchTerm}/{offset}',['uses'=>'notes@searchService']);
    Route::post('/notes/notecreate',['uses'=>'notes@noteCreate'])->middleware('auth:api');
    Route::post('/notes/noteupdate','notes@noteUpdate')->middleware('auth:api');
    Route::post('/notes/notedelete','notes@noteDelete')->middleware('auth:api');
    Route::post('/notes/uploadimagestest','notes@ImgTest')->middleware('auth:api');
  //  Route::get('/notes/getcurrentuser',['uses'=>'notes@getCurrentUser']);
  //  Route::get('/checkuser','notes@getUser');

  //  Route::post('/users/posttest',['uses'=>'users@postTest','as' => 'testing']);
    Route::post('/users/login','users@login');
    Route::post('/users/register','users@register');
    Route::post('/users/cookieslogin','users@cookiesLogin')->middleware('auth:api');
    Route::post('/users/getAll','users@getUsersAndProfiles')->middleware('auth:api');
    Route::post('/users/update','users@updateUser');
    Route::post('/users/delete','users@deleteUser');
    Route::get('/users/search/{term}','users@search');
    Route::post('/users/updateProfile','users@profileRequestHandler')->middleware('auth:api');

    Route::post('/comments/sendcomment','comments@sendComment')->middleware('auth:api');
    Route::get('/comments/getcomments/{noteId}','comments@getComments');
    Route::post('/comments/user','comments@getCommentsForUser');
    Route::post('/comments/update','comments@updateComment')->middleware('auth:api');
    Route::post('/comments/delete','comments@deleteComment')->middleware('auth:api');
});
